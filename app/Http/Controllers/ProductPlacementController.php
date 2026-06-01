<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Style;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductPlacementController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with([
            'style',
            'tags',
            'previews' => fn($q) => $q->orderBy('sort_order')->limit(1),
        ])->where('status', 'published');

        // ─── Filter Style ─────────────────────────────────────────
        // $request->style berisi nama style, misal: "Popart"
        if ($request->filled('style')) {
            $query->whereHas('style', fn($q) => $q->where('name', $request->style));
        }

        // ─── Filter Tags ──────────────────────────────────────────
        // $request->tags berisi array label tag, misal: ["BMX", "Rock"]
        // Logic: OR — produk yang punya minimal 1 tag dari yang dipilih
        if ($request->filled('tags')) {
            $tags = is_array($request->tags)
                ? array_filter($request->tags)           // sudah array, bersihkan kosong
                : array_filter(explode(',', $request->tags)); // fallback string

            if (!empty($tags)) {
                $query->whereHas('tags', fn($q) => $q->whereIn('label', $tags));
            }
        }

        $products = $query->latest()->get()->map(fn($p) => [
            'id'    => $p->id,
            'name'  => $p->title,
            'style' => $p->style?->name ?? '',
            'tags'  => $p->tags->pluck('label')->toArray(),
            'image' => $p->thumbnail
                ? asset('storage/' . $p->thumbnail)
                : asset('assets/image/shop/placeholder.png'),
        ]);

        // ─── Data filter untuk DropDownCategory ───────────────────
        $styles = Style::orderBy('name')->pluck('name')->toArray();

        $tagGroups = Category::where('is_tag_group', true)
            ->with('tags:id,label,category_id')
            ->orderBy('name')
            ->get()
            ->map(fn($group) => [
                'name'  => $group->name,
                'items' => $group->tags->pluck('label')->toArray(),
            ])
            ->toArray();

        return Inertia::render('Landing/Gsap/Product/Index', [
            'products'  => $products,
            'styles'    => $styles,
            'tagGroups' => $tagGroups,
            // Kembalikan filter aktif ke Vue agar state sinkron
            'activeFilters' => [
                'style' => $request->style ?? '',
                'tags'  => is_array($request->tags)
                    ? array_values(array_filter($request->tags))
                    : [],
            ],
        ]);
    }

    public function show(int $id)
    {
        $product = Product::with([
            'category',
            'style',
            'tags',
            'previews' => fn($q) => $q->orderBy('sort_order'),
            'files',
            'reviews.user',
        ])->where('status', 'published')->findOrFail($id);

        return Inertia::render('Landing/Gsap/Product/Detail', [
            'product' => [
                'id'          => $product->id,
                'name'        => $product->title,
                'description' => $product->description,
                'price'       => $product->is_free ? 'Free' : '$ ' . number_format($product->price, 0, ',', '.'),
                'is_free'     => $product->is_free,
                'style'       => $product->style?->name,
                'category'    => $product->category?->name,
                'tags'        => $product->tags->pluck('label')->toArray(),
                'thumbnail'   => $product->thumbnail ? asset('storage/' . $product->thumbnail) : null,
                'previews'    => $product->previews->map(fn($p) => [
                    'id'         => $p->id,
                    'type'       => $p->type,
                    'label'      => $p->label ?? null,
                    'url'        => asset('storage/' . $p->path),
                    'sort_order' => $p->sort_order,
                ]),
                'files' => $product->files->map(fn($f) => [
                    'id'        => $f->id,
                    'file_type' => strtoupper($f->file_type),
                    'url'       => asset('storage/' . $f->file_path),
                ]),
            ],
        ]);
    }
}
