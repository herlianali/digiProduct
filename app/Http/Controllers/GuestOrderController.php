<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Models\OrderItem;
use App\Models\OrderCustomer;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GuestOrderController extends Controller
{
    /**
     * POST /orders/guest
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'     => 'required|string|max:255',
            'customer_email'    => 'required|email|max:255',
            'customer_phone'    => 'nullable|string|max:20',
            'customer_whatsapp' => 'nullable|string|max:20',
            'customer_company'  => 'nullable|string|max:255',
            'customer_address'  => 'nullable|string|max:500',
            'customer_city'     => 'nullable|string|max:100',
            'notes'             => 'nullable|string|max:1000',
            'voucher_code'      => 'nullable|string|max:50',
            'items'             => 'required|array|min:1',
            'items.*.product_id'    => 'required|integer|exists:products,id',
            'items.*.product_title' => 'required|string|max:255',
            'items.*.price'         => 'required|numeric|min:0',
            'items.*.quantity'      => 'required|integer|min:1',
        ]);

        // ── Hitung subtotal ───────────────────────────────────────
        $subtotal = collect($validated['items'])->sum(
            fn($item) => $item['price'] * $item['quantity']
        );

        // ── Voucher ───────────────────────────────────────────────
        $discount     = 0;
        $voucherModel = null;

        if (!empty($validated['voucher_code'])) {
            $voucherModel = Voucher::where('code', strtoupper($validated['voucher_code']))
                ->where('is_active', true)
                ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
                ->where(fn($q) => $q->whereNull('usage_limit')->orWhereColumn('usage_count', '<', 'usage_limit'))
                ->first();

            if ($voucherModel) {
                $discount = $voucherModel->discount_type === 'percentage'
                    ? ($subtotal * $voucherModel->discount_value) / 100
                    : min($voucherModel->discount_value, $subtotal);
            }
        }

        $total = max(0, $subtotal - $discount);

        // ── Simpan ke DB ──────────────────────────────────────────
        $order = DB::transaction(function () use ($validated, $subtotal, $discount, $total, $voucherModel) {

            $order = Orders::create([
                'order_number'   => 'ORD-' . strtoupper(Str::random(8)),
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'total'          => $total,
                'payment_status' => 'pending',
                'status'         => 'pending',
            ]);

            OrderCustomer::create([
                'order_id'  => $order->id,
                'name'      => $validated['customer_name'],
                'email'     => $validated['customer_email'],
                'phone'     => $validated['customer_phone']    ?? null,
                'whatsapp'  => $validated['customer_whatsapp'] ?? $validated['customer_phone'] ?? null,
                'company'   => $validated['customer_company']  ?? null,
                'address'   => $validated['customer_address']  ?? null,
                'city'      => $validated['customer_city']     ?? null,
                'notes'     => $validated['notes']             ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $item['product_id'],
                    'product_title' => $item['product_title'],
                    'price'         => $item['price'],
                    'quantity'      => $item['quantity'],
                ]);
            }

            if ($voucherModel) {
                $voucherModel->increment('usage_count');
            }

            return $order->load(['customer', 'items']);
        });

        $waUrl = $this->buildWhatsAppUrl($order, $validated);

        return response()->json([
            'success'      => true,
            'order_number' => $order->order_number,
            'total'        => $order->total,
            'whatsapp_url' => $waUrl,
        ], 201);
    }

    /**
     * Bangun URL wa.me dengan template pesan pemesanan
     */
    private function buildWhatsAppUrl(Orders $order, array $data): string
    {
        $businessWa = config('app.whatsapp_number');

        $items = $order->items->map(function ($item) {
            $price = number_format($item->price, 0, ',', '.');
            return "  - {$item->product_title} (x{$item->quantity}) — $ {$price}";
        })->implode("\n");

        $subtotal = number_format($order->subtotal, 0, ',', '.');
        $discount = number_format($order->discount, 0, ',', '.');
        $total    = number_format($order->total,    0, ',', '.');

        $voucherLine = $order->discount > 0
            ? "\nVoucher      : {$data['voucher_code']} (- $ {$discount})"
            : '';

        $notesLine = !empty($data['notes'])
            ? "\nCatatan      : {$data['notes']}"
            : '';

        $message = <<<MSG
            Halo Supplaybox! 👋

            Saya ingin melakukan pemesanan:

            ================================
            DETAIL PESANAN
            ================================
            Order #      : {$order->order_number}

            {$items}

            ================================
            RINGKASAN HARGA
            ================================
            Subtotal      : $ {$subtotal}{$voucherLine}
            TOTAL         : $ {$total}

            ================================
            DATA PEMESAN
            ================================
            Nama          : {$data['customer_name']}
            Email         : {$data['customer_email']}
        MSG;

        if (!empty($data['customer_phone'])) {
            $message .= "\nTelepon       : {$data['customer_phone']}";
        }
        if (!empty($data['customer_company'])) {
            $message .= "\nPerusahaan    : {$data['customer_company']}";
        }
        if (!empty($data['customer_city'])) {
            $message .= "\nKota          : {$data['customer_city']}";
        }

        $message .= $notesLine;
        $message .= "\n\nMohon konfirmasi pesanan saya. Terima kasih.";

        return 'https://wa.me/' . $businessWa . '?text=' . rawurlencode(trim($message));
    }

    /**
     * POST /vouchers/validate
     */
    public function validateVoucher(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50',
        ]);

        $voucher = Voucher::where('code', strtoupper($request->code))
            ->where('is_active', true)
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
            ->where(fn($q) => $q->whereNull('usage_limit')->orWhereColumn('usage_count', '<', 'usage_limit'))
            ->first();

        if (!$voucher) {
            return response()->json([
                'valid'   => false,
                'message' => 'Voucher code is invalid or has expired.',
            ], 422);
        }

        return response()->json([
            'valid'   => true,
            'voucher' => [
                'code'           => $voucher->code,
                'discount_type'  => $voucher->discount_type,
                'discount_value' => $voucher->discount_value,
            ],
        ]);
    }
}
