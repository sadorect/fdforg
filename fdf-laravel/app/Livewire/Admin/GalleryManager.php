<?php

namespace App\Livewire\Admin;

use App\Models\GalleryItem;
use App\Services\GalleryImageProcessor;
use App\Support\AdminPermissions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class GalleryManager extends AdminComponent
{
    use WithFileUploads;
    use WithPagination;

    protected array $adminAbilities = [AdminPermissions::MANAGE_GALLERY];

    public $search = '';

    public $typeFilter = '';

    public $statusFilter = '';

    public $showForm = false;

    public $editing = false;

    public $galleryId;

    public $title = '';

    public $slug = '';

    public $description = '';

    public $type = 'activity';

    public $event_name = '';

    public $captured_at = '';

    public $is_featured = false;

    public $sort_order = 0;

    public $status = 'published';

    public $images = [];

    public $existingImagePaths = [];

    public $removedImagePaths = [];

    protected $paginationTheme = 'tailwind';

    private const MAX_IMAGE_KB = 4096;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $items = GalleryItem::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%')
                        ->orWhere('event_name', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->typeFilter !== '', fn ($query) => $query->where('type', $this->typeFilter))
            ->when($this->statusFilter !== '', fn ($query) => $query->where('status', $this->statusFilter))
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(12);

        $imageProcessor = app(GalleryImageProcessor::class);

        return view('livewire.admin.gallery-manager', [
            'items' => $items,
            'maxImageKb' => self::MAX_IMAGE_KB,
            'uploadMaxFilesize' => ini_get('upload_max_filesize') ?: 'unknown',
            'postMaxSize' => ini_get('post_max_size') ?: 'unknown',
            'imageOptimizationAvailable' => $imageProcessor->isOptimizationAvailable(),
        ])->layout('layouts.admin')
            ->title('Gallery Management');
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editing = false;
    }

    public function edit(int $id): void
    {
        $item = GalleryItem::findOrFail($id);

        $this->galleryId = $item->id;
        $this->title = $item->title;
        $this->slug = $item->slug;
        $this->description = $item->description ?? '';
        $this->type = $item->type;
        $this->event_name = $item->event_name ?? '';
        $this->captured_at = $item->captured_at?->format('Y-m-d') ?? '';
        $this->is_featured = (bool) $item->is_featured;
        $this->sort_order = (int) $item->sort_order;
        $this->status = $item->status;
        $this->images = [];
        $this->existingImagePaths = $item->normalized_image_paths;
        $this->removedImagePaths = [];
        $this->showForm = true;
        $this->editing = true;
    }

    public function save(): void
    {
        $data = $this->validate($this->rules());

        $allImagePaths = $this->existingImagePaths;

        $imageProcessor = app(GalleryImageProcessor::class);
        foreach ($this->images as $image) {
            $allImagePaths[] = $imageProcessor->storeOptimized($image);
        }

        $allImagePaths = array_values(array_filter($allImagePaths));

        if (count($allImagePaths) === 0) {
            $this->addError('images', 'At least one image is required.');

            return;
        }

        $data['image_paths'] = $allImagePaths;
        $data['image_path'] = $allImagePaths[0];

        if ($data['event_name'] === '') {
            $data['event_name'] = null;
        }

        if ($data['captured_at'] === '') {
            $data['captured_at'] = null;
        }

        if ($this->editing) {
            GalleryItem::findOrFail($this->galleryId)->update($data);

            foreach ($this->removedImagePaths as $removedImagePath) {
                if ($removedImagePath !== '' && Storage::disk('public')->exists($removedImagePath)) {
                    Storage::disk('public')->delete($removedImagePath);
                }
            }

            session()->flash('success', 'Gallery item updated successfully.');
        } else {
            GalleryItem::create($data);
            session()->flash('success', 'Gallery item created successfully.');
        }

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $item = GalleryItem::findOrFail($id);

        foreach ($item->normalized_image_paths as $path) {
            if ($path !== '' && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $item->delete();

        session()->flash('success', 'Gallery item deleted successfully.');
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    public function updatedTitle(string $value): void
    {
        if (! $this->editing || $this->slug === '') {
            $this->slug = Str::slug($value);
        }
    }

    public function removeExistingImage(int $index): void
    {
        if (! isset($this->existingImagePaths[$index])) {
            return;
        }

        $this->removedImagePaths[] = $this->existingImagePaths[$index];
        unset($this->existingImagePaths[$index]);
        $this->existingImagePaths = array_values($this->existingImagePaths);
    }

    public function removeSelectedImage(int $index): void
    {
        if (! isset($this->images[$index])) {
            return;
        }

        unset($this->images[$index]);
        $this->images = array_values($this->images);
    }

    private function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('gallery_items', 'slug')->ignore($this->galleryId),
            ],
            'description' => ['nullable', 'string', 'max:5000'],
            'type' => ['required', Rule::in(['activity', 'event'])],
            'event_name' => ['nullable', 'string', 'max:255'],
            'captured_at' => ['nullable', 'date'],
            'is_featured' => ['boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'images' => [$this->editing ? 'nullable' : 'required', 'array', 'min:1'],
            'images.*' => ['image', 'max:'.self::MAX_IMAGE_KB, 'mimes:jpg,jpeg,png,webp'],
        ];
    }

    private function resetForm(): void
    {
        $this->reset([
            'galleryId',
            'title',
            'slug',
            'description',
            'type',
            'event_name',
            'captured_at',
            'is_featured',
            'sort_order',
            'status',
            'images',
            'existingImagePaths',
            'removedImagePaths',
            'showForm',
            'editing',
        ]);

        $this->type = 'activity';
        $this->status = 'published';
        $this->sort_order = 0;
        $this->is_featured = false;
    }
}
