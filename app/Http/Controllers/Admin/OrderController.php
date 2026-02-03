<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * List Orders
     */
    public function index(Request $request)
    {
        $orders = Orders::query()
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->latest()
            ->paginate(10)
            ->through(fn ($order) => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => '-', // sementara
                'total' => number_format($order->total, 0, ',', '.'),
                'payment_status' => $order->payment_status,
                'status' => $order->status,
                'created_at' => $order->created_at->format('d M Y'),
            ]);

        return Inertia::render('Orders/Index', [
            'orders' => $orders->items(),
            'meta' => [
                'total' => $orders->total(),
            ]
        ]);
    }

    /**
     * Detail Order
     */
    public function show(Orders $order)
    {
        $order->load(['user', 'items.product']);

        return Inertia::render('Orders/Show', [
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer' => [
                    'name' => $order->user?->name,
                    'email' => $order->user?->email,
                ],
                'subtotal' => $order->subtotal,
                'discount' => $order->discount,
                'total' => $order->total,
                'payment_status' => $order->payment_status,
                'status' => $order->status,
                'paid_at' => $order->paid_at,
                'items' => $order->items->map(fn ($item) => [
                    'product_title' => $item->product_title,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'total' => $item->price * $item->quantity,
                ]),
            ]
        ]);
    }

    /**
     * Update Status Order
     */
    public function update(Request $request, Orders $order)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,shipped,completed,cancelled',
            'payment_status' => 'nullable|in:pending,paid,failed',
        ]);

        $order->update([
            'status' => $request->status,
            'payment_status' => $request->payment_status ?? $order->payment_status,
            'paid_at' => $request->payment_status === 'paid'
                ? now()
                : $order->paid_at,
        ]);

        return back()->with('success', 'Order berhasil diperbarui');
    }

    /**
     * Delete Order
     */
    public function destroy(Orders $order)
    {
        $order->items()->delete();
        $order->delete();

        return redirect()
            ->route('orders.index')
            ->with('success', 'Order berhasil dihapus');
    }
}
