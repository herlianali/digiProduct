<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Style;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CatalogController extends Controller
{
    // ─── Index: load semua data untuk 3 tab ───────────────────────
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'categories');

        return Inertia::render('Catalog/Index', [
            'tab' => $tab,

            // Tab 1: Kategori Produk
            'categories' => Category::withCount('products')
                ->where('is_tag_group', false)
                ->latest()->get(),

            // Tab 2: Tag Group + Tags
            'tagGroups' => Category::with(['tags' => fn($q) => $q->withCount('products' /* pivot */)])
                ->withCount('tags')
                ->where('is_tag_group', true)
                ->latest()->get(),

            // Tab 3: Styles
            'styles' => Style::withCount('products')->latest()->get(),

            'stats' => [
                'categories' => Category::where('is_tag_group', false)->count(),
                'tagGroups'  => Category::where('is_tag_group', true)->count(),
                'tags'       => Tag::count(),
                'styles'     => Style::count(),
            ],
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    // CATEGORY PRODUCT
    // ══════════════════════════════════════════════════════════════

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
        ], ['name.required' => 'Nama kategori wajib diisi.', 'name.unique' => 'Nama sudah digunakan.']);

        Category::create([
            'name'         => $data['name'],
            'slug'         => $this->uniqueSlug($data['name'], 'categories'),
            'is_tag_group' => false,
        ]);

        return back()->with('success', "Kategori \"{$data['name']}\" ditambahkan.");
    }

    public function updateCategory(Request $request, Category $category)
    {
        $this->guardCategory($category);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', "unique:categories,name,{$category->id}"],
        ], ['name.required' => 'Nama kategori wajib diisi.', 'name.unique' => 'Nama sudah digunakan.']);

        // Re-generate slug hanya jika nama berubah
        $update = ['name' => $data['name']];
        if ($data['name'] !== $category->name) {
            $update['slug'] = $this->uniqueSlug($data['name'], 'categories', $category->id);
        }

        $category->update($update);

        return back()->with('success', "Kategori \"{$category->name}\" diperbarui.");
    }

    public function destroyCategory(Category $category)
    {
        $this->guardCategory($category);
        $name = $category->name;
        $category->delete();
        return back()->with('success', "Kategori \"{$name}\" dihapus.");
    }

    // ══════════════════════════════════════════════════════════════
    // TAG GROUP (categories where is_tag_group = true)
    // ══════════════════════════════════════════════════════════════

    public function storeTagGroup(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
        ], ['name.required' => 'Nama group wajib diisi.', 'name.unique' => 'Nama sudah digunakan.']);

        Category::create([
            'name'         => $data['name'],
            'slug'         => $this->uniqueSlug($data['name'], 'categories'),
            'is_tag_group' => true,
        ]);

        return back()->with('success', "Tag group \"{$data['name']}\" ditambahkan.");
    }

    public function updateTagGroup(Request $request, Category $tagGroup)
    {
        $this->guardTagGroup($tagGroup);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', "unique:categories,name,{$tagGroup->id}"],
        ], ['name.required' => 'Nama group wajib diisi.', 'name.unique' => 'Nama sudah digunakan.']);

        $update = ['name' => $data['name']];
        if ($data['name'] !== $tagGroup->name) {
            $update['slug'] = $this->uniqueSlug($data['name'], 'categories', $tagGroup->id);
        }

        $tagGroup->update($update);

        return back()->with('success', "Tag group \"{$tagGroup->name}\" diperbarui.");
    }

    public function destroyTagGroup(Category $tagGroup)
    {
        $this->guardTagGroup($tagGroup);

        if ($tagGroup->tags()->exists()) {
            return back()->withErrors(['tagGroup' => 'Hapus semua tag di dalam group ini terlebih dahulu.']);
        }

        $name = $tagGroup->name;
        $tagGroup->delete();
        return back()->with('success', "Tag group \"{$name}\" dihapus.");
    }

    // ══════════════════════════════════════════════════════════════
    // TAGS
    // ══════════════════════════════════════════════════════════════

    public function storeTag(Request $request)
    {
        $data = $request->validate([
            'label'       => ['required', 'string', 'max:100', 'unique:tags,label'],
            'category_id' => ['required', 'exists:categories,id'],
        ], [
            'label.required'       => 'Label tag wajib diisi.',
            'label.unique'         => 'Label tag sudah digunakan.',
            'category_id.required' => 'Pilih tag group.',
            'category_id.exists'   => 'Tag group tidak valid.',
        ]);

        Tag::create([
            'label'       => $data['label'],
            'slug'        => $this->uniqueSlug($data['label'], 'tags', null, 'slug'),
            'category_id' => $data['category_id'],
        ]);

        return back()->with('success', "Tag \"{$data['label']}\" ditambahkan.");
    }

    public function updateTag(Request $request, Tag $tag)
    {
        $data = $request->validate([
            'label'       => ['required', 'string', 'max:100', "unique:tags,label,{$tag->id}"],
            'category_id' => ['required', 'exists:categories,id'],
        ], [
            'label.required'       => 'Label tag wajib diisi.',
            'label.unique'         => 'Label tag sudah digunakan.',
            'category_id.required' => 'Pilih tag group.',
        ]);

        $update = [
            'label'       => $data['label'],
            'category_id' => $data['category_id'],
        ];
        if ($data['label'] !== $tag->label) {
            $update['slug'] = $this->uniqueSlug($data['label'], 'tags', $tag->id, 'slug');
        }

        $tag->update($update);

        return back()->with('success', "Tag \"{$tag->label}\" diperbarui.");
    }

    public function destroyTag(Tag $tag)
    {
        $label = $tag->label;
        $tag->delete();
        return back()->with('success', "Tag \"{$label}\" dihapus.");
    }

    // ══════════════════════════════════════════════════════════════
    // STYLES
    // ══════════════════════════════════════════════════════════════

    public function storeStyle(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:styles,name'],
        ], ['name.required' => 'Nama style wajib diisi.', 'name.unique' => 'Nama style sudah digunakan.']);

        Style::create(['name' => $data['name']]);

        return back()->with('success', "Style \"{$data['name']}\" ditambahkan.");
    }

    public function updateStyle(Request $request, Style $style)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', "unique:styles,name,{$style->id}"],
        ], ['name.required' => 'Nama style wajib diisi.', 'name.unique' => 'Nama style sudah digunakan.']);

        $style->update(['name' => $data['name']]);

        return back()->with('success', "Style \"{$style->name}\" diperbarui.");
    }

    public function destroyStyle(Style $style)
    {
        if ($style->products()->exists()) {
            return back()->withErrors(['style' => 'Style masih digunakan oleh produk, tidak bisa dihapus.']);
        }
        $name = $style->name;
        $style->delete();
        return back()->with('success', "Style \"{$name}\" dihapus.");
    }

    // ══════════════════════════════════════════════════════════════
    // HELPERS
    // ══════════════════════════════════════════════════════════════

    private function uniqueSlug(string $name, string $table, ?int $exceptId = null, string $column = 'slug'): string
    {
        $base    = Str::slug($name);
        $slug    = $base;
        $counter = 1;

        while (
            DB::table($table)
                ->where($column, $slug)
                ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
                ->exists()
        ) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private function guardCategory(Category $category): void
    {
        if ($category->is_tag_group) abort(404);
    }

    private function guardTagGroup(Category $category): void
    {
        if (!$category->is_tag_group) abort(404);
    }
}