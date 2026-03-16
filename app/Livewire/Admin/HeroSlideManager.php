<?php

namespace App\Livewire\Admin;

use App\Models\HeroSlide;
use App\Support\AdminPermissions;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class HeroSlideManager extends AdminComponent
{
    use WithFileUploads;
    use WithPagination;

    protected array $adminAbilities = [AdminPermissions::MANAGE_HERO_SLIDES];

    public ?int $slideId = null;

    public bool $showForm = false;

    public bool $editing = false;

    public string $search = '';

    public string $title = '';

    public string $subtitle = '';

    public string $content = '';

    public string $cta_label = '';

    public string $cta_url = '';

    public $image;

    public ?string $existing_image_path = null;

    public int $sort_order = 0;

    public bool $is_active = true;

    protected function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'content' => 'nullable|string|max:1500',
            'cta_label' => 'nullable|string|max:80',
            'cta_url' => 'nullable|url|max:255',
            'image' => 'nullable|image|max:3072',
            'sort_order' => 'required|integer|min:0|max:9999',
            'is_active' => 'boolean',
        ];

        return $rules;
    }

    public function render()
    {
        $slides = HeroSlide::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($inner) {
                    $inner->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('subtitle', 'like', '%'.$this->search.'%')
                        ->orWhere('content', 'like', '%'.$this->search.'%');
                });
            })
            ->ordered()
            ->paginate(10);

        return view('livewire.admin.hero-slide-manager', [
            'slides' => $slides,
        ])->layout('layouts.admin')
            ->title('Hero Slides');
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editing = false;
        $this->sort_order = (int) HeroSlide::max('sort_order') + 1;
    }

    public function edit(int $id): void
    {
        $slide = HeroSlide::findOrFail($id);

        $this->slideId = $slide->id;
        $this->title = $slide->title;
        $this->subtitle = (string) $slide->subtitle;
        $this->content = (string) $slide->content;
        $this->cta_label = (string) $slide->cta_label;
        $this->cta_url = (string) $slide->cta_url;
        $this->existing_image_path = $slide->image_path;
        $this->sort_order = (int) $slide->sort_order;
        $this->is_active = (bool) $slide->is_active;
        $this->showForm = true;
        $this->editing = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        $payload = [
            'title' => $data['title'],
            'subtitle' => $data['subtitle'] ?: null,
            'content' => $data['content'] ?: null,
            'cta_label' => $data['cta_label'] ?: null,
            'cta_url' => $data['cta_url'] ?: null,
            'sort_order' => $data['sort_order'],
            'is_active' => $data['is_active'],
        ];

        if ($this->image) {
            $payload['image_path'] = $this->image->store('hero-slides', 'public');
        }

        if ($this->editing && $this->slideId) {
            $slide = HeroSlide::findOrFail($this->slideId);
            if ($this->image && $slide->image_path) {
                Storage::disk('public')->delete($slide->image_path);
            }
            $slide->update($payload);
            session()->flash('success', 'Hero slide updated.');
        } else {
            HeroSlide::create($payload);
            session()->flash('success', 'Hero slide created.');
        }

        $this->resetForm();
    }

    public function toggleStatus(int $id): void
    {
        $slide = HeroSlide::findOrFail($id);
        $slide->update(['is_active' => ! $slide->is_active]);
        session()->flash('success', 'Hero slide status updated.');
    }

    public function delete(int $id): void
    {
        $slide = HeroSlide::findOrFail($id);
        if ($slide->image_path) {
            Storage::disk('public')->delete($slide->image_path);
        }
        $slide->delete();
        session()->flash('success', 'Hero slide deleted.');
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset([
            'slideId',
            'title',
            'subtitle',
            'content',
            'cta_label',
            'cta_url',
            'image',
            'existing_image_path',
            'showForm',
            'editing',
        ]);

        $this->sort_order = 0;
        $this->is_active = true;
    }
}
