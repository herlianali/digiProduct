<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Orders::query()
            ->with('customer')   // eager load customer
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->through(fn($order) => [
                'id'             => $order->id,
                'order_number'   => $order->order_number,
                'customer_name'  => $order->customer?->name  ?? '—',
                'customer_email' => $order->customer?->email ?? '',
                'customer_phone' => $order->customer?->whatsapp ?? $order->customer?->phone ?? null,
                'total'          => number_format($order->total, 0, ',', '.'),
                'payment_status' => $order->payment_status,
                'status'         => $order->status,
                'created_at'     => $order->created_at->format('d M Y'),
            ]);

        return Inertia::render('Orders/Index', [
            'orders' => $orders->items(),
            'meta'   => ['total' => $orders->total()],
        ]);
    }

    public function show(Orders $order)
    {
        // Eksplisit foreign key karena nama model "Orders"
        $order->load([
            'customer',
            'items',     // sudah difix foreign key-nya di model
        ]);

        return Inertia::render('Orders/Show', [
            'order' => [
                'id'             => $order->id,
                'order_number'   => $order->order_number,
                'subtotal'       => $order->subtotal,
                'discount'       => $order->discount,
                'total'          => $order->total,
                'payment_status' => $order->payment_status,
                'status'         => $order->status,
                'paid_at'        => $order->paid_at?->format('d M Y H:i'),
                'created_at'     => $order->created_at->format('d M Y H:i'),

                // Data customer dari tabel order_customers
                'customer' => $order->customer ? [
                    'name'     => $order->customer->name,
                    'email'    => $order->customer->email,
                    'phone'    => $order->customer->phone,
                    'whatsapp' => $order->customer->whatsapp,
                    'company'  => $order->customer->company,
                    'city'     => $order->customer->city,
                    'address'  => $order->customer->address,
                    'notes'    => $order->customer->notes,
                ] : null,

                'items' => $order->items->map(fn($item) => [
                    'product_title' => $item->product_title,
                    'price'         => $item->price,
                    'quantity'      => $item->quantity,
                    'total'         => $item->price * $item->quantity,
                ]),
            ],
        ]);
    }

    public function update(Request $request, Orders $order)
    {
        $request->validate([
            'status'         => 'required|in:pending,processing,shipped,completed,cancelled',
            'payment_status' => 'nullable|in:pending,paid,failed,refunded',
        ]);

        $order->update([
            'status'         => $request->status,
            'payment_status' => $request->payment_status ?? $order->payment_status,
            'paid_at'        => $request->payment_status === 'paid' && !$order->paid_at
                ? now()
                : $order->paid_at,
        ]);

        return back()->with('success', 'Order berhasil diperbarui');
    }

    public function destroy(Orders $order)
    {
        // Hapus relasi dulu (dengan foreign key eksplisit via model)
        $order->items()->delete();
        $order->customer()->delete();
        $order->delete();

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Order berhasil dihapus');
    }
}
