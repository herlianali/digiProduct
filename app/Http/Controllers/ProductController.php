<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->latest()
            ->get()
            ->map(function ($product) {
                // Pastikan semua field ada
                return [
                    'id'         => $product->id,
                    'title'      => $product->title ?? 'No Title',
                    'category'   => $product->category->name ?? '-',
                    'price'      => $product->is_free ? 'Free' : 'Rp ' . number_format($product->price ?? 0, 0, ',', '.'),
                    'status'     => ucfirst($product->status ?? 'draft'),
                    'created_at' => $product->created_at ? $product->created_at->format('d/m/Y H:i') : '-',
                ];
            })
            ->filter() // Hapus null jika ada
            ->values() // Reset keys
            ->toArray();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'filters' => request()->only(['search', 'status']),
            'meta' => [
                'total' => count($products),
            ],
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
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price'       => 'required_if:is_free,false|nullable|numeric|min:0',
            'is_free'     => 'boolean',
            'status'      => 'required|in:draft,published,archived',
            'description' => 'required|string',
            'images'      => 'required|array|min:1',
            'images.*'    => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        try {
            $product = Product::create([
                'title'       => $validated['title'],
                'slug'        => Str::slug($validated['title']),
                'category_id' => $validated['category_id'],
                'price'       => $validated['is_free'] ? 0 : ($validated['price'] ?? 0),
                'is_free'     => $validated['is_free'] ?? false,
                'status'      => $validated['status'],
                'description' => $validated['description'],
            ]);

            if ($request->hasFile('images')) {
                $sortOrder = 0;
                
                foreach ($request->file('images') as $image) {
                    // Generate unique filename
                    $filename = Str::random(20) . '.' . $image->getClientOriginalExtension();
                    
                    // Define paths
                    $folder = 'product-previews';
                    $path = $image->storeAs($folder, $filename, 'public');
                    
                    // Anda juga bisa resize gambar jika perlu
                    $resizedImage = Image::make($image)->resize(800, 800, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                    Storage::disk('public')->put($folder . '/' . $filename, $resizedImage->encode());
                    
                    // Save to product_previews table
                    $product->previews()->create([
                        'type' => 'image',
                        'path' => $path,
                        'sort_order' => $sortOrder,
                    ]);
                    
                    $sortOrder++;
                }
                
                // Jika ingin set thumbnail di products table (opsional)
                $firstPreview = $product->previews()->first();
                if ($firstPreview) {
                    $product->update([
                        'thumbnail' => $firstPreview->path
                    ]);
                }
            }

            return redirect()->route('products.index')
                ->with('success', 'Produk berhasil ditambahkan');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }


    public function edit(Product $product)
    {
        // Load previews dengan URL yang benar
        $existingImages = $product->previews()->get()->map(function ($preview) {
            return [
                'id' => $preview->id,
                'path' => $preview->path,
                'url' => asset('storage/' . $preview->path),
                'filename' => basename($preview->path),
                'extension' => pathinfo($preview->path, PATHINFO_EXTENSION),
                'is_primary' => $preview->sort_order === 0,
                'sort_order' => $preview->sort_order,
            ];
        })->toArray();

        return inertia('Products/Form', [
            'mode' => 'edit',
            'product' => $product,
            'existingImages' => $existingImages,
            'categories' => Category::all(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        // Untuk update, gambar tidak wajib jika sudah ada
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price'       => 'required_if:is_free,false|nullable|numeric|min:0',
            'is_free'     => 'boolean',
            'status'      => 'required|in:draft,published,archived',
            'description' => 'required|string',
            'images'      => 'nullable|array',
            'images.*'    => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'delete_images.*' => 'nullable|exists:product_previews,id',
        ], [
            'title.required' => 'Nama produk wajib diisi.',
            'category_id.required' => 'Tipe produk wajib dipilih.',
            'category_id.exists' => 'Tipe produk tidak valid.',
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
                'category_id' => $validated['category_id'],
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