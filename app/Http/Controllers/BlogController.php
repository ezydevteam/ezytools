<?php

namespace App\Http\Controllers;

use App\Models\AdSpace;
use App\Models\Post;
use Inertia\Inertia;

class BlogController extends Controller
{
    public function index()
    {
        $posts = Post::with('author:id,name,avatar')
            ->published()
            ->latestFirst()
            ->paginate(9);

        $ads = [
            'top' => AdSpace::active()->forPosition('blog-top')->visibleTo(auth()->check() ? (auth()->user()->isPro() ? 'none' : 'free') : 'guest')->first(),
            'sidebar' => AdSpace::active()->forPosition('blog-sidebar')->visibleTo(auth()->check() ? (auth()->user()->isPro() ? 'none' : 'free') : 'guest')->first(),
        ];

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
            'ads' => $ads,
        ]);
    }

    public function show(string $slug)
    {
        $post = Post::with('author:id,name,avatar')->published()->where('slug', $slug)->firstOrFail();

        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->latestFirst()
            ->take(3)
            ->get(['id', 'title', 'slug', 'excerpt', 'featured_image', 'published_at']);

        $ads = [
            'top' => AdSpace::active()->forPosition('blog-top')->visibleTo(auth()->check() ? (auth()->user()->isPro() ? 'none' : 'free') : 'guest')->first(),
            'sidebar' => AdSpace::active()->forPosition('blog-sidebar')->visibleTo(auth()->check() ? (auth()->user()->isPro() ? 'none' : 'free') : 'guest')->first(),
        ];

        $schema = app(\App\Services\SchemaService::class);

        return Inertia::render('Blog/Show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'ads' => $ads,
            'schemas' => [
                $schema->article($post),
                $schema->breadcrumb([
                    ['name' => 'Home', 'url' => url('/')],
                    ['name' => 'Blog', 'url' => route('blog.index')],
                    ['name' => $post->title, 'url' => route('blog.show', $post->slug)],
                ]),
            ],
        ]);
    }
}
