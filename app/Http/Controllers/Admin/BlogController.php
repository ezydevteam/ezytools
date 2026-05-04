<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::with('author:id,name')
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Blog/Index', [
            'posts' => $posts,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Blog/Form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_published' => 'boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
        $validated['user_id'] = $request->user()->id;

        if ($validated['is_published'] ?? false) {
            $validated['published_at'] = now();
        }

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        Post::create($validated);

        return redirect()->route('admin.blog.index')->with('success', 'Blog post created successfully.');
    }

    public function edit(Post $blog)
    {
        return Inertia::render('Admin/Blog/Form', [
            'post' => $blog,
        ]);
    }

    public function update(Request $request, Post $blog)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug,' . $blog->id,
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_published' => 'boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);

        // Handle publish status change
        if (($validated['is_published'] ?? false) && !$blog->published_at) {
            $validated['published_at'] = now();
        } elseif (!($validated['is_published'] ?? false)) {
            $validated['published_at'] = null;
        }

        if ($request->hasFile('featured_image')) {
            if ($blog->featured_image) {
                \Storage::disk('public')->delete($blog->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        } else {
            unset($validated['featured_image']);
        }

        $blog->update($validated);

        return redirect()->route('admin.blog.index')->with('success', 'Blog post updated successfully.');
    }

    public function destroy(Post $blog)
    {
        if ($blog->featured_image) {
            \Storage::disk('public')->delete($blog->featured_image);
        }

        $blog->delete();

        return back()->with('success', 'Blog post deleted.');
    }
}
