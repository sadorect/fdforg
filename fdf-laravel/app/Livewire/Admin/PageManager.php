<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Page;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class PageManager extends Component
{
    use WithPagination, WithFileUploads;

    public $pageId;
    public $showForm = false;
    public $editing = false;
    public $title;
    public $slug;
    public $content;
    public $meta_description;
    public $status = 'published';
    public $featured_image;
    public $search = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:pages,slug',
        'content' => 'required|string',
        'meta_description' => 'nullable|string|max:255',
        'status' => 'required|in:draft,published,archived',
        'featured_image' => 'nullable|image|max:2048',
    ];

    public function render()
    {
        $pages = Page::when($this->search, function ($query) {
            return $query->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('slug', 'like', '%' . $this->search . '%');
        })
        ->orderBy('title')
        ->paginate(10);

        return view('livewire.admin.page-manager', [
            'pages' => $pages
        ])->layout('layouts.admin')
          ->title('Page Management');
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editing = false;
    }

    public function store()
    {
        $this->validate();

        $page = Page::create([
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'meta_description' => $this->meta_description,
            'status' => $this->status,
        ]);

        if ($this->featured_image) {
            $page->update([
                'meta_image' => $this->featured_image->store('pages', 'public'),
            ]);
        }

        session()->flash('success', 'Page created successfully.');
        $this->resetForm();
    }

    public function edit(int $id)
    {
        $page = Page::findOrFail($id);

        $this->pageId = $page->id;
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->content = $page->content;
        $this->meta_description = $page->meta_description;
        $this->status = $page->status;
        $this->showForm = true;
        $this->editing = true;
    }

    public function update()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $this->pageId,
            'content' => 'required|string',
            'meta_description' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published,archived',
            'featured_image' => 'nullable|image|max:2048',
        ]);

        $page = Page::findOrFail($this->pageId);
        $page->update([
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'meta_description' => $this->meta_description,
            'status' => $this->status,
        ]);

        if ($this->featured_image) {
            $page->update([
                'meta_image' => $this->featured_image->store('pages', 'public'),
            ]);
        }

        session()->flash('success', 'Page updated successfully.');
        $this->resetForm();
    }

    public function delete(int $id)
    {
        $page = Page::findOrFail($id);
        $page->delete();
        session()->flash('success', 'Page deleted successfully.');
    }

    public function toggleStatus(int $id)
    {
        $page = Page::findOrFail($id);
        $page->update([
            'status' => $page->status === 'published' ? 'draft' : 'published',
        ]);
        session()->flash('success', 'Page status updated.');
    }

    public function updatedTitle()
    {
        $this->slug = Str::slug($this->title);
    }

    public function cancel()
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'pageId',
            'title',
            'slug',
            'content',
            'meta_description',
            'featured_image',
            'showForm',
            'editing',
        ]);
        $this->status = 'published';
    }
}
