<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            'categories' => Category::all()
        ]);
    }

    public function store(Request $request)
{
    // Debug - lihat apa yang dikirim
    dd($request->all());
    
    $validated = $request->validate([
        'title'       => 'required|string|max:255',
        'type'        => 'required|in:font,template,illustration,ui-kit,other',
        'price'       => 'required_if:is_free,false|nullable|numeric|min:0',
        'is_free'     => 'boolean',
        'status'      => 'required|in:draft,published,archived',
        'description' => 'required|string',
        'images'      => 'required|array|min:1',
        'images.*'    => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240',
    ]);

    try {
        // Cari category berdasarkan type
        $category = Category::where('name', ucfirst($validated['type'] . 's'))->first();
        
        // Jika tidak ada, buat baru
        if (!$category) {
            $category = Category::create([
                'name' => ucfirst($validated['type'] . 's'),
                'slug' => Str::slug($validated['type'] . 's')
            ]);
        }

        $product = Product::create([
            'title'       => $validated['title'],
            'slug'        => Str::slug($validated['title']),
            'type'        => $validated['type'],
            'category_id' => $category->id, // Gunakan category_id yang sesuai
            'price'       => $validated['is_free'] ? 0 : ($validated['price'] ?? 0),
            'is_free'     => $validated['is_free'] ?? false,
            'status'      => $validated['status'],
            'description' => $validated['description'],
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('products/previews', 'public');

                $product->previews()->create([
                    'type'       => 'image',
                    'path'       => $path,
                    'sort_order' => $index,
                ]);

                if ($index === 0) {
                    $product->update(['thumbnail' => $path]);
                }
            }
        }

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan');
            
    } catch (\Exception $e) {
        Log::error('Error creating product: ' . $e->getMessage());
        return back()
            ->withInput()
            ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
    }
}

    public function edit(Product $product)
    {
        return Inertia::render('Products/Form', [
            'mode' => 'edit',
            'product' => $product,
            'categories' => Category::all(),
            'existingImages' => $product->previews->map(fn ($img) => [
                'id'   => $img->id,
                'url'  => asset('storage/' . $img->path),
                'name' => basename($img->path),
                'type' => $img->type,
                'size' => $img->size ?? 0,
            ]),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        // Untuk update, gambar tidak wajib jika sudah ada
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'type'        => 'required|in:font,template,illustration,ui-kit,other',
            'price'       => 'required_if:is_free,false|nullable|numeric|min:0',
            'is_free'     => 'boolean',
            'status'      => 'required|in:draft,published,archived',
            'description' => 'required|string',
            'images'      => 'nullable|array',
            'images.*'    => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'delete_images.*' => 'nullable|exists:product_previews,id',
        ], [
            'title.required' => 'Nama produk wajib diisi.',
            'type.required' => 'Tipe produk wajib dipilih.',
            'type.in' => 'Tipe produk tidak valid.',
            'price.required_if' => 'Harga wajib diisi untuk produk berbayar.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga tidak boleh kurang dari 0.',
            'status.required' => 'Status produk wajib dipilih.',
            'status.in' => 'Status produk tidak valid.',
            'description.required' => 'Deskripsi produk wajib diisi.',
            'images.array' => 'Format gambar tidak valid.',
            'images.*.image' => 'File harus berupa gambar.',
            'images.*.mimes' => 'Format gambar harus: jpeg, png, jpg, gif, atau webp.',
            'images.*.max' => 'Ukuran gambar maksimal 10MB.',
            'delete_images.*.exists' => 'Gambar yang akan dihapus tidak ditemukan.',
        ]);

        try {
            $product->update([
                'title'       => $validated['title'],
                'slug'        => Str::slug($validated['title']),
                'type'        => $validated['type'],
                'price'       => $validated['is_free'] ? 0 : ($validated['price'] ?? 0),
                'is_free'     => $validated['is_free'] ?? false,
                'status'      => $validated['status'],
                'description' => $validated['description'],
            ]);

            // Hapus gambar yang dipilih
            if ($request->filled('delete_images')) {
                $product->previews()
                    ->whereIn('id', $request->delete_images)
                    ->delete();
            }

            // Upload gambar baru
            if ($request->hasFile('images')) {
                $order = $product->previews()->max('sort_order') ?? 0;

                foreach ($request->file('images') as $file) {
                    $path = $file->store('products/previews', 'public');

                    $product->previews()->create([
                        'type' => 'image',
                        'path' => $path,
                        'sort_order' => ++$order,
                    ]);
                }
            }

            // Update thumbnail jika ada gambar
            if ($first = $product->previews()->orderBy('sort_order')->first()) {
                $product->update(['thumbnail' => $first->path]);
            }

            return redirect()
                ->route('products.index')
                ->with('success', 'Produk berhasil diperbarui');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function destroy(Product $product)
    {
        try {
            $product->delete();
            
            return back()->with('success', 'Produk berhasil dihapus');
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus produk: ' . $e->getMessage()]);
        }
    }
}