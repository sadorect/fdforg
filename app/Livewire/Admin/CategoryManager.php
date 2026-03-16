<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Support\AdminPermissions;
use Illuminate\Validation\Rule;
use Livewire\WithPagination;

class CategoryManager extends AdminComponent
{
    use WithPagination;

    protected array $adminAbilities = [AdminPermissions::MANAGE_CATEGORIES];

    public $search = '';

    public $typeFilter = '';

    public $showForm = false;

    public $editing = false;

    public $categoryId;

    public $name = '';

    public $slug = '';

    public $description = '';

    public $type = 'blog';

    public $is_active = true;

    public $sort_order = 0;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $categories = Category::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->typeFilter !== '', fn ($query) => $query->where('type', $this->typeFilter))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.admin.category-manager', [
            'categories' => $categories,
        ])->layout('layouts.admin')
            ->title('Category Management');
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editing = false;
    }

    public function edit(int $id): void
    {
        $category = Category::findOrFail($id);

        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description ?? '';
        $this->type = $category->type;
        $this->is_active = (bool) $category->is_active;
        $this->sort_order = (int) $category->sort_order;
        $this->showForm = true;
        $this->editing = true;
    }

    public function save(): void
    {
        $data = $this->validate($this->rules());

        if ($this->editing) {
            Category::findOrFail($this->categoryId)->update($data);
            session()->flash('success', 'Category updated successfully.');
        } else {
            Category::create($data);
            session()->flash('success', 'Category created successfully.');
        }

        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $category = Category::findOrFail($id);

        if ($category->blogPosts()->exists() || $category->courses()->exists()) {
            session()->flash('error', 'Cannot delete category with linked blog posts or courses.');

            return;
        }

        $category->delete();
        session()->flash('success', 'Category deleted successfully.');
    }

    public function toggleStatus(int $id): void
    {
        $category = Category::findOrFail($id);
        $category->update(['is_active' => ! $category->is_active]);
    }

    public function cancel(): void
    {
        $this->resetForm();
    }

    public function updatedName(string $value): void
    {
        if (! $this->editing) {
            $this->slug = str($value)->slug()->toString();
        }
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($this->categoryId),
            ],
            'description' => ['nullable', 'string'],
            'type' => ['required', Rule::in(['blog', 'course', 'resource'])],
            'is_active' => ['boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ];
    }

    private function resetForm(): void
    {
        $this->reset([
            'categoryId',
            'name',
            'slug',
            'description',
            'type',
            'is_active',
            'sort_order',
            'showForm',
            'editing',
        ]);
        $this->type = 'blog';
        $this->is_active = true;
        $this->sort_order = 0;
    }
}
