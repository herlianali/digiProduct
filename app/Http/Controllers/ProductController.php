<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        return Inertia::render('Products/Index', [
            'products' => Product::latest()->get()->map(function ($product) {
                return [
                    'id'       => $product->id,
                    'title'    => $product->title,
                    'type'     => $product->type,
                    'price'    => $product->is_free ? 'Free' : number_format($product->price, 0, ',', '.'),
                    'status'   => ucfirst($product->status),
                ];
            }),
        ]);
    }

    public function create()
    {
        return Inertia::render('Products/Form', [
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'type'        => 'required',
            'price'       => 'nullable|numeric',
            'is_free'     => 'boolean',
            'status'      => 'required',
            'description' => 'required',
        ]);

        Product::create([
            ...$validated,
            'slug' => Str::slug($validated['title']),
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit(Product $product)
    {
        return Inertia::render('Products/Form', [
            'mode'    => 'edit',
            'product' => $product,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'type'        => 'required',
            'price'       => 'nullable|numeric',
            'is_free'     => 'boolean',
            'status'      => 'required',
            'description' => 'required',
        ]);

        $product->update([
            ...$validated,
            'slug' => Str::slug($validated['title']),
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus');
    }
}
