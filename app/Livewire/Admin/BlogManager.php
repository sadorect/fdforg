<?php

namespace App\Livewire\Admin;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\User;
use App\Support\AdminPermissions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class BlogManager extends AdminComponent
{
    use WithFileUploads;
    use WithPagination;

    protected array $adminAbilities = [AdminPermissions::MANAGE_BLOG];

    public $search = '';

    public $category_filter = '';

    public $status_filter = '';

    public $showForm = false;

    public $editing = false;

    public $post_id;

    // Form fields
    public $title;

    public $slug;

    public $excerpt;

    public $content;

    public $category_id;

    public $author_id;

    public $status = 'draft';

    public $is_featured = false;

    public $published_at;

    public $featured_image;

    public $tags = [];

    public $new_tag;

    protected $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:blog_posts,slug',
        'excerpt' => 'required|string|max:500',
        'content' => 'required|string',
        'category_id' => 'nullable|exists:categories,id',
        'author_id' => 'required|exists:users,id',
        'status' => 'required|in:draft,published,archived',
        'is_featured' => 'boolean',
        'published_at' => 'nullable|date',
        'featured_image' => 'nullable|image|max:2048',
        'tags' => 'array',
    ];

    public function mount()
    {
        $this->author_id = User::where('is_admin', true)->value('id') ?? User::query()->value('id');
        $this->published_at = now()->format('Y-m-d\TH:i');
    }

    public function render()
    {
        $query = BlogPost::with(['category', 'author'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('content', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->category_filter, function ($query) {
                $query->where('category_id', $this->category_filter);
            })
            ->when($this->status_filter, function ($query) {
                $query->where('status', $this->status_filter);
            })
            ->orderBy('created_at', 'desc');

        $posts = $query->paginate(10);
        $categories = Category::where('type', 'blog')->where('is_active', true)->get();
        $authors = User::where('is_admin', true)->orWhere('bio', '!=', null)->get();

        return view('livewire.admin.blog-manager', [
            'posts' => $posts,
            'categories' => $categories,
            'authors' => $authors,
        ])->layout('layouts.admin');
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editing = false;
    }

    public function edit(int $postId)
    {
        $post = BlogPost::findOrFail($postId);

        $this->post_id = $post->id;
        $this->title = $post->title;
        $this->slug = $post->slug;
        $this->excerpt = $post->excerpt;
        $this->content = $post->content;
        $this->category_id = $post->category_id;
        $this->author_id = $post->author_id;
        $this->status = $post->status;
        $this->is_featured = $post->is_featured;
        $this->published_at = $post->published_at?->format('Y-m-d\TH:i');
        $this->tags = is_array($post->tags) ? $post->tags : [];

        $this->showForm = true;
        $this->editing = true;
    }

    public function save()
    {
        $rules = $this->rules;

        if ($this->editing) {
            $rules['slug'] = 'required|string|max:255|unique:blog_posts,slug,'.$this->post_id;
        }

        $this->validate($rules);

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'category_id' => $this->normalizeNullableForeignKey($this->category_id),
            'author_id' => $this->author_id,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'tags' => array_values($this->tags),
        ];

        if ($this->status === 'published' && ! $this->published_at) {
            $data['published_at'] = now();
        } elseif ($this->published_at) {
            $data['published_at'] = $this->published_at;
        }

        if ($this->featured_image) {
            $imagePath = $this->featured_image->store('blog-images', 'public');
            $data['featured_image'] = $imagePath;
        }

        if ($this->editing) {
            BlogPost::findOrFail($this->post_id)->update($data);
            $this->dispatch('blog-updated', 'Blog post updated successfully!');
        } else {
            BlogPost::create($data);
            $this->dispatch('blog-created', 'Blog post created successfully!');
        }

        $this->resetForm();
    }

    public function delete(int $postId)
    {
        $post = BlogPost::findOrFail($postId);

        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }

        $post->delete();
        $this->dispatch('blog-deleted', 'Blog post deleted successfully!');
    }

    public function toggleFeatured(int $postId)
    {
        $post = BlogPost::findOrFail($postId);

        $post->update(['is_featured' => ! $post->is_featured]);
        $this->dispatch('blog-updated', 'Post featured status updated!');
    }

    public function duplicate(int $postId)
    {
        $post = BlogPost::findOrFail($postId);

        $newPost = $post->replicate();
        $newPost->title = $post->title.' (Copy)';
        $newPost->slug = $post->slug.'-copy-'.time();
        $newPost->status = 'draft';
        $newPost->published_at = null;
        $newPost->views = 0;
        $newPost->save();

        $this->dispatch('blog-created', 'Blog post duplicated successfully!');
    }

    public function cancel()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'post_id', 'title', 'slug', 'excerpt', 'content', 'category_id',
            'author_id', 'status', 'is_featured', 'published_at', 'featured_image', 'tags',
        ]);

        $this->showForm = false;
        $this->editing = false;
        $this->author_id = User::where('is_admin', true)->value('id') ?? User::query()->value('id');
        $this->published_at = now()->format('Y-m-d\TH:i');
    }

    public function updatedTitle()
    {
        $this->slug = Str::slug($this->title);
    }

    public function addTag()
    {
        if (! empty($this->new_tag) && ! in_array($this->new_tag, $this->tags)) {
            $this->tags[] = $this->new_tag;
            $this->new_tag = '';
        }
    }

    public function removeTag($tag)
    {
        $this->tags = array_values(array_diff($this->tags, [$tag]));
    }

    public function getPostCountProperty()
    {
        return BlogPost::count();
    }

    public function getPublishedCountProperty()
    {
        return BlogPost::where('status', 'published')->count();
    }

    public function getDraftCountProperty()
    {
        return BlogPost::where('status', 'draft')->count();
    }

    public function getFeaturedCountProperty()
    {
        return BlogPost::where('is_featured', true)->count();
    }

    private function normalizeNullableForeignKey(mixed $value): ?int
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return (int) $value;
    }
}
