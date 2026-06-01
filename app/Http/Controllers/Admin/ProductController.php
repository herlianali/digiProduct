<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Style;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductController extends Controller
{
    // ─── Index ────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Product::with(['category', 'style', 'tags']);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('style_id')) {
            $query->where('style_id', $request->style_id);
        }

        $products = $query->latest()->get()
            ->map(fn($p) => [
                'id'         => $p->id,
                'title'      => $p->title ?? 'No Title',
                'category'   => $p->category?->name ?? '-',
                'style'      => $p->style?->name ?? '-',
                'tags'       => $p->tags->pluck('label')->join(', ') ?: '-',
                'price'      => $p->is_free ? 'Free' : '$ ' . number_format($p->price ?? 0, 0, ',', '.'),
                'status'     => $p->status ?? 'draft',
                'created_at' => $p->created_at?->format('d/m/Y H:i') ?? '-',
            ])
            ->values()
            ->toArray();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'filters'  => $request->only(['search', 'status', 'style_id']),
            'styles'   => Style::orderBy('name')->get(['id', 'name']),
            'meta'     => ['total' => count($products)],
        ]);
    }

    // ─── Create ───────────────────────────────────────────────────
    public function create()
    {
        return Inertia::render('Products/Form', [
            'mode'       => 'create',
            'categories' => Category::where('is_tag_group', false)->orderBy('name')->get(['id', 'name']),
            'styles'     => Style::orderBy('name')->get(['id', 'name']),
            'tagGroups'  => Category::where('is_tag_group', true)
                ->with('tags:id,label,category_id')
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    // ─── Store ────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'style_id'    => 'nullable|exists:styles,id',
            'tags'        => 'nullable|array',
            'tags.*'      => 'exists:tags,id',
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
                'slug'        => $this->uniqueSlug($validated['title']),
                'category_id' => $validated['category_id'] ?? null,
                'style_id'    => $validated['style_id'] ?? null,
                'price'       => ($validated['is_free'] ?? false) ? 0 : ($validated['price'] ?? 0),
                'is_free'     => $validated['is_free'] ?? false,
                'status'      => $validated['status'],
                'description' => $validated['description'],
            ]);

            // Sync tags
            if (!empty($validated['tags'])) {
                $product->tags()->sync($validated['tags']);
            }

            // Upload images
            if ($request->hasFile('images')) {
                $sortOrder = 0;
                foreach ($request->file('images') as $image) {
                    $path = $this->storeImage($image, $product->slug);
                    $product->previews()->create([
                        'type'       => 'image',
                        'path'       => $path,
                        'sort_order' => $sortOrder++,
                    ]);
                }

                if ($first = $product->previews()->orderBy('sort_order')->first()) {
                    $product->update(['thumbnail' => $first->path]);
                }
            }

            return redirect()->route('admin.products.index')
                ->with('success', "Produk \"{$product->title}\" berhasil ditambahkan.");
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // ─── Show ─────────────────────────────────────────────────────
    public function show(Product $product)
    {
        return Inertia::render('Products/Show', [
            'product' => $product->load(['category', 'style', 'tags', 'previews', 'files', 'reviews.user']),
        ]);
    }

    // ─── Edit ─────────────────────────────────────────────────────
    public function edit(Product $product)
    {
        $existingImages = $product->previews()
            ->orderBy('sort_order')
            ->get()
            ->map(fn($p) => [
                'id'         => $p->id,
                'path'       => $p->path,
                'url'        => asset(Storage::url($p->path)),
                'filename'   => basename($p->path),
                'extension'  => pathinfo($p->path, PATHINFO_EXTENSION),
                'is_primary' => $p->sort_order === 0,
                'sort_order' => $p->sort_order,
            ])->toArray();

        return Inertia::render('Products/Form', [
            'mode'           => 'edit',
            'productId'      => $product->id, // Add this
            'product'        => array_merge($product->toArray(), [
                'tag_ids' => $product->tags->pluck('id')->toArray(),
            ]),
            'existingImages' => $existingImages,
            'categories'     => Category::where('is_tag_group', false)->orderBy('name')->get(['id', 'name']),
            'styles'         => Style::orderBy('name')->get(['id', 'name']),
            'tagGroups'      => Category::where('is_tag_group', true)
                ->with('tags:id,label,category_id')
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    // ─── Update ───────────────────────────────────────────────────
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'category_id'   => 'nullable|exists:categories,id',
            'style_id'      => 'nullable|exists:styles,id',
            'tags'          => 'nullable|array',
            'tags.*'        => 'exists:tags,id',
            'price'         => 'required_if:is_free,false|nullable|numeric|min:0',
            'is_free'       => 'boolean',
            'status'        => 'required|in:draft,published,archived',
            'description'   => 'required|string',
            'images'        => 'nullable|array',
            'images.*'      => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'delete_images' => 'nullable|string',
            'image_order'   => 'nullable|string',
        ]);

        try {
            $product->update([
                'title'       => $validated['title'],
                'slug'        => $this->uniqueSlug($validated['title'], $product->id),
                'category_id' => $validated['category_id'] ?? null,
                'style_id'    => $validated['style_id'] ?? null,
                'price'       => ($validated['is_free'] ?? false) ? 0 : ($validated['price'] ?? 0),
                'is_free'     => $validated['is_free'] ?? false,
                'status'      => $validated['status'],
                'description' => $validated['description'],
            ]);

            // Sync tags
            $product->tags()->sync($validated['tags'] ?? []);

            // Hapus gambar yang dipilih
            if ($request->filled('delete_images')) {
                $deleteIds = json_decode($request->delete_images, true) ?? [];
                if (!empty($deleteIds)) {
                    $previews = $product->previews()->whereIn('id', $deleteIds)->get();
                    foreach ($previews as $preview) {
                        Storage::disk('public')->delete($preview->path);
                    }
                    $product->previews()->whereIn('id', $deleteIds)->delete();
                }
            }

            // Upload gambar baru
            if ($request->hasFile('images')) {
                $order = $product->previews()->max('sort_order') ?? -1;
                foreach ($request->file('images') as $file) {
                    $path = $this->storeImage($file, $product->slug);
                    $product->previews()->create([
                        'type'       => 'image',
                        'path'       => $path,
                        'sort_order' => ++$order,
                    ]);
                }
            }

            // Update image order if provided
            if ($request->filled('image_order')) {
                $imageOrder = json_decode($request->image_order, true) ?? [];
                if (!empty($imageOrder)) {
                    foreach ($imageOrder as $index => $imageId) {
                        $product->previews()
                            ->where('id', $imageId)
                            ->update(['sort_order' => $index]);
                    }
                }
            }

            // Update thumbnail based on sort_order
            $firstImage = $product->previews()->orderBy('sort_order')->first();
            if ($firstImage) {
                $product->update(['thumbnail' => $firstImage->path]);
            }

            return redirect()->route('admin.products.index')
                ->with('success', "Produk \"{$product->title}\" berhasil diperbarui.");
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // ─── Destroy ──────────────────────────────────────────────────
    public function destroy(Product $product)
    {
        try {
            // Hapus semua previews
            foreach ($product->previews as $preview) {
                Storage::disk('public')->delete($preview->path);
            }
            $product->previews()->delete();

            $title = $product->title;
            $product->delete();
            return back()->with('success', "Produk \"{$title}\" berhasil dihapus.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus produk: ' . $e->getMessage()]);
        }
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Simpan gambar ke storage/public/product-previews/
     * Nama file: {slug-produk}_{YYYYMMDD_HHmmss}_{random4}.{ext}
     * Contoh: autobiography_20240315_143022_a3f1.jpg
     */
    private function storeImage(UploadedFile $file, string $productSlug): string
    {
        $ext      = strtolower($file->getClientOriginalExtension());
        $datetime = now()->format('Ymd_His');
        $rand     = Str::random(4);
        $filename = "{$productSlug}_{$datetime}_{$rand}.{$ext}";
        $folder   = 'product-previews';

        // Pastikan folder ada
        Storage::disk('public')->makeDirectory($folder);

        // Simpan file — storeAs mengembalikan path relatif dari disk
        $path = $file->storeAs($folder, $filename, 'public');

        return $path; // e.g. "product-previews/autobiography_20240315_143022_a3f1.jpg"
    }

    private function uniqueSlug(string $title, ?int $exceptId = null): string
    {
        $base    = Str::slug($title);
        $slug    = $base;
        $counter = 1;

        while (
            Product::where('slug', $slug)
            ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->exists()
        ) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    public function reorderImages(Request $request, Product $product)
    {
        $request->validate([
            'image_order' => 'required|array',
            'image_order.*' => 'exists:product_previews,id'
        ]);

        foreach ($request->image_order as $index => $imageId) {
            $product->previews()
                ->where('id', $imageId)
                ->update(['sort_order' => $index]);
        }

        // Update thumbnail
        $firstImage = $product->previews()->orderBy('sort_order')->first();
        if ($firstImage) {
            $product->update(['thumbnail' => $firstImage->path]);
        }

        return back()->with('success', "Urutan gambar berhasil diperbarui.");
    }
}
