<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Course;
use App\Models\Category;
use App\Models\User;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class CourseManager extends Component
{
    use WithFileUploads;

    public $courses;
    public $categories;
    public $instructors;
    public $selectedCourse = null;
    public $showCreateForm = false;
    public $showEditForm = false;
    
    // Form fields
    public $title;
    public $description;
    public $content;
    public $category_id;
    public $instructor_id;
    public $featured_image;
    public $intro_video_url;
    public $difficulty_level = 'beginner';
    public $duration_minutes = 0;
    public $status = 'draft';
    public $pricing_model = 'paid';
    public $max_students;
    public $is_certificate_enabled = true;
    public $is_featured = false;
    public $price = 0;
    public $currency_code = 'USD';
    
    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string|max:500',
        'content' => 'nullable|string',
        'category_id' => 'nullable|exists:categories,id',
        'instructor_id' => 'required|exists:users,id',
        'featured_image' => 'nullable|image|max:2048',
        'intro_video_url' => 'nullable|url|max:2048',
        'difficulty_level' => 'required|in:beginner,intermediate,advanced',
        'duration_minutes' => 'required|integer|min:0',
        'status' => 'required|in:draft,published,archived',
        'max_students' => 'nullable|integer|min:1',
        'is_certificate_enabled' => 'boolean',
        'price' => 'required|numeric|min:0',
        'currency_code' => 'required|in:USD,NGN',
        'is_featured' => 'boolean',
    ];

    public function mount()
    {
        $this->loadCourses();
        $this->categories = Category::where('type', 'course')->orderBy('name')->get();
        $this->instructors = User::orderBy('name')->get();
    }

    public function loadCourses()
    {
        $this->courses = Course::with(['category', 'instructor'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function createCourse()
    {
        $this->resetForm();
        $this->showCreateForm = true;
        $this->showEditForm = false;
    }

    public function storeCourse()
    {
        $this->validate();

        $course = new Course();
        $course->title = $this->title;
        $course->slug = Str::slug($this->title);
        $course->description = $this->description;
        $course->content = $this->content;
        $course->category_id = $this->category_id;
        $course->instructor_id = $this->instructor_id;
        $course->intro_video_url = $this->intro_video_url;
        $course->difficulty_level = $this->difficulty_level;
        $course->duration_minutes = $this->duration_minutes;
        $course->status = $this->status;
        $course->max_students = $this->max_students ?: null;
        $course->is_certificate_enabled = $this->is_certificate_enabled;
        $course->price = $this->pricing_model === 'free' ? 0 : $this->price;
        $course->currency_code = $this->currency_code;
        $course->is_featured = $this->is_featured;
        
        if ($this->featured_image) {
            $course->featured_image = $this->featured_image->store('courses', 'public');
        }
        
        $course->save();

        $this->showCreateForm = false;
        $this->loadCourses();
        $this->dispatch('course-saved', 'Course created successfully!');
    }

    public function editCourse($id)
    {
        $this->selectedCourse = Course::findOrFail($id);
        $this->title = $this->selectedCourse->title;
        $this->description = $this->selectedCourse->description;
        $this->content = $this->selectedCourse->content;
        $this->category_id = $this->selectedCourse->category_id;
        $this->instructor_id = $this->selectedCourse->instructor_id;
        $this->intro_video_url = $this->selectedCourse->intro_video_url;
        $this->difficulty_level = $this->selectedCourse->difficulty_level;
        $this->duration_minutes = $this->selectedCourse->duration_minutes;
        $this->status = $this->selectedCourse->status;
        $this->pricing_model = (float) $this->selectedCourse->price > 0 ? 'paid' : 'free';
        $this->max_students = $this->selectedCourse->max_students;
        $this->is_certificate_enabled = $this->selectedCourse->is_certificate_enabled;
        $this->price = $this->selectedCourse->price;
        $this->currency_code = $this->selectedCourse->currency_code;
        $this->is_featured = $this->selectedCourse->is_featured;
        
        $this->showEditForm = true;
        $this->showCreateForm = false;
    }

    public function updateCourse()
    {
        $this->validate();

        $this->selectedCourse->title = $this->title;
        $this->selectedCourse->slug = Str::slug($this->title);
        $this->selectedCourse->description = $this->description;
        $this->selectedCourse->content = $this->content;
        $this->selectedCourse->category_id = $this->category_id;
        $this->selectedCourse->instructor_id = $this->instructor_id;
        $this->selectedCourse->intro_video_url = $this->intro_video_url;
        $this->selectedCourse->difficulty_level = $this->difficulty_level;
        $this->selectedCourse->duration_minutes = $this->duration_minutes;
        $this->selectedCourse->status = $this->status;
        $this->selectedCourse->max_students = $this->max_students ?: null;
        $this->selectedCourse->is_certificate_enabled = $this->is_certificate_enabled;
        $this->selectedCourse->price = $this->pricing_model === 'free' ? 0 : $this->price;
        $this->selectedCourse->currency_code = $this->currency_code;
        $this->selectedCourse->is_featured = $this->is_featured;
        
        if ($this->featured_image) {
            $this->selectedCourse->featured_image = $this->featured_image->store('courses', 'public');
        }
        
        $this->selectedCourse->save();

        $this->showEditForm = false;
        $this->loadCourses();
        $this->dispatch('course-saved', 'Course updated successfully!');
    }

    public function deleteCourse($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
        $this->loadCourses();
        $this->dispatch('course-saved', 'Course deleted successfully!');
    }

    public function toggleFeatured($id)
    {
        $course = Course::findOrFail($id);
        $course->is_featured = !$course->is_featured;
        $course->save();
        $this->loadCourses();
    }

    public function togglePublished($id)
    {
        $course = Course::findOrFail($id);
        $course->status = $course->status === 'published' ? 'draft' : 'published';
        $course->save();
        $this->loadCourses();
    }

    public function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->content = '';
        $this->category_id = '';
        $this->instructor_id = '';
        $this->featured_image = null;
        $this->intro_video_url = '';
        $this->difficulty_level = 'beginner';
        $this->duration_minutes = 0;
        $this->status = 'draft';
        $this->pricing_model = 'paid';
        $this->max_students = '';
        $this->is_certificate_enabled = true;
        $this->is_featured = false;
        $this->price = 0;
        $this->currency_code = 'USD';
        $this->selectedCourse = null;
    }

    public function updatedPricingModel(string $value): void
    {
        if ($value === 'free') {
            $this->price = 0;
        }
    }

    public function updatedPrice($value): void
    {
        if ((float) $value > 0) {
            $this->pricing_model = 'paid';
        }
    }

    public function cancel()
    {
        $this->showCreateForm = false;
        $this->showEditForm = false;
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.admin.course-manager')
            ->layout('layouts.admin')
            ->title('Course Management');
    }
}
