<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Articles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Articles::with('author')->latest();

        // Filter status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search !== '') {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $request->search . '%');
            });
        }

        $articles = $query->paginate(10);

        return Inertia::render('Articles/Index', [
            'articles' => $articles->items(),
            'meta' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
                'links' => $articles->linkCollection()->toArray(),
            ],
            'filters' => $request->only(['status', 'search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Articles/Form', [
            'mode' => 'create',
            'authorName' => auth()->user()->name
        ]);
    }

    public function edit(Articles $article)
    {
        // Authorization check
        if ($article->author_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        return Inertia::render('Articles/Form', [
            'mode' => 'edit',
            'article' => $article,
            'authorName' => $article->author->name ?? auth()->user()->name
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:articles,slug',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail_file')) {
            $file = $request->file('thumbnail_file');
            $fileName = 'articles/' . time() . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
            
            $path = $file->storeAs('public', $fileName);
            $validated['thumbnail'] = str_replace('public/', '', $path);
        }

        // Remove file from validated array
        unset($validated['thumbnail_file']);

        // Set author
        $validated['author_id'] = auth()->id();

        // Handle published_at
        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        Articles::create($validated);

        return redirect()->route('articles.index')
            ->with('success', 'Artikel berhasil dibuat.');
    }

    public function update(Request $request, Articles $article)
    {
        // Authorization check
        if ($article->author_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|unique:articles,slug,' . $article->id,
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'thumbnail_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'thumbnail' => 'nullable|string', // For keeping existing thumbnail
        ]);

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail_file')) {
            // Delete old thumbnail if exists
            if ($article->thumbnail && Storage::exists('public/' . $article->thumbnail)) {
                Storage::delete('public/' . $article->thumbnail);
            }

            $file = $request->file('thumbnail_file');
            $fileName = 'articles/' . time() . '_' . Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
            
            $path = $file->storeAs('public', $fileName);
            $validated['thumbnail'] = str_replace('public/', '', $path);
        } elseif (empty($validated['thumbnail'])) {
            // If no new file and thumbnail is empty, delete existing thumbnail
            if ($article->thumbnail && Storage::exists('public/' . $article->thumbnail)) {
                Storage::delete('public/' . $article->thumbnail);
            }
            $validated['thumbnail'] = null;
        }

        // Remove file from validated array
        unset($validated['thumbnail_file']);

        // Handle published_at
        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        } elseif ($validated['status'] !== 'published') {
            $validated['published_at'] = null;
        }

        $article->update($validated);

        return redirect()->route('articles.index')
            ->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Articles $article)
    {
        $article->delete();

        return redirect()->route('articles.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }

}
