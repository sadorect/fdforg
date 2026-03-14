<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $category = null;
        $categorySlug = $request->string('category')->toString();

        $categories = Category::active()
            ->byType('blog')
            ->ordered()
            ->withCount([
                'blogPosts as published_posts_count' => function ($query) {
                    $query->published();
                },
            ])
            ->get();

        $baseQuery = BlogPost::published()
            ->with(['author', 'category'])
            ->when($categorySlug !== '', function ($query) use (&$category, $categorySlug) {
                $category = Category::active()
                    ->byType('blog')
                    ->where('slug', $categorySlug)
                    ->first();

                $query->whereHas('category', function ($categoryQuery) use ($categorySlug) {
                    $categoryQuery->where('slug', $categorySlug)
                        ->where('type', 'blog')
                        ->where('is_active', true);
                });
            });

        $featuredPost = (clone $baseQuery)
            ->featured()
            ->recent()
            ->first();

        $posts = $baseQuery
            ->when($featuredPost, function ($query) use ($featuredPost) {
                $query->whereKeyNot($featuredPost->id);
            })
            ->recent()
            ->paginate(9)
            ->withQueryString();

        $popularPosts = (clone $baseQuery)
            ->when($featuredPost, function ($query) use ($featuredPost) {
                $query->whereKeyNot($featuredPost->id);
            })
            ->popular()
            ->take(3)
            ->get();

        $blogStats = [
            'article_count' => BlogPost::published()->count(),
            'category_count' => $categories->where('published_posts_count', '>', 0)->count(),
            'author_count' => BlogPost::published()->distinct()->count('author_id'),
        ];

        return view('blog.index', compact(
            'posts',
            'featuredPost',
            'categories',
            'category',
            'popularPosts',
            'blogStats'
        ));
    }

    public function show(BlogPost $blogPost): View
    {
        if (! $blogPost->isPublished()) {
            abort(404);
        }

        $blogPost->load(['author', 'category']);
        $blogPost->incrementViews();

        $relatedPosts = BlogPost::published()
            ->with('category')
            ->where('id', '!=', $blogPost->id)
            ->when($blogPost->category_id, function ($query) use ($blogPost) {
                $query->where('category_id', $blogPost->category_id);
            })
            ->recent()
            ->take(3)
            ->get();

        $latestPosts = BlogPost::published()
            ->with('category')
            ->where('id', '!=', $blogPost->id)
            ->recent()
            ->take(3)
            ->get();

        return view('blog.show', [
            'post' => $blogPost,
            'relatedPosts' => $relatedPosts,
            'latestPosts' => $latestPosts,
        ]);
    }
}
