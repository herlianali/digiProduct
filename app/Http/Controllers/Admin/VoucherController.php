<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = Voucher::query();

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        $vouchers = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return Inertia::render('Vouchers/Index', [
            'vouchers' => $vouchers->items(),
            'meta'     => ['total' => $vouchers->total()],
            'filters'  => $request->only(['search', 'is_active']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Vouchers/Form', ['mode' => 'create']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'         => 'required|string|max:50|unique:vouchers,code',
            'discount_pct' => 'nullable|integer|min:0|max:100',
            'expires_at'   => 'nullable|date',
            'is_active'    => 'boolean',
        ]);

        Voucher::create($data);

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher berhasil ditambahkan.');
    }

    public function edit(Voucher $voucher)
    {
        return Inertia::render('Admin/Vouchers/Form', [
            'mode'    => 'edit',
            'voucher' => $voucher,
        ]);
    }

    public function update(Request $request, Voucher $voucher)
    {
        $data = $request->validate([
            'code'         => 'required|string|max:50|unique:vouchers,code,' . $voucher->id,
            'discount_pct' => 'nullable|integer|min:0|max:100',
            'expires_at'   => 'nullable|date',
            'is_active'    => 'boolean',
        ]);

        $voucher->update($data);

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher berhasil diperbarui.');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher berhasil dihapus.');
    }
}
