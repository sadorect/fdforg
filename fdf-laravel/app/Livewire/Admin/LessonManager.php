<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use App\Models\Lesson;
use App\Support\AdminPermissions;
use Illuminate\Support\Str;

class LessonManager extends AdminComponent
{
    protected array $adminAbilities = [AdminPermissions::MANAGE_LESSONS];

    public $lessons;

    public $courses;

    public $selectedLesson = null;

    public $showCreateForm = false;

    public $showEditForm = false;

    public $selectedCourseId = null;

    // Form fields
    public $title;

    public $description;

    public $content;

    public $course_id;

    public $video_url;

    public $duration_minutes = 0;

    public $sort_order = 0;

    public $type = 'video';

    public $is_published = false;

    public $is_free = false;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:500',
        'content' => 'required|string',
        'course_id' => 'required|exists:courses,id',
        'video_url' => 'nullable|url',
        'duration_minutes' => 'required|integer|min:0',
        'sort_order' => 'required|integer|min:0',
        'type' => 'required|in:video,text,quiz,assignment',
    ];

    public function mount()
    {
        $this->loadLessons();
        $this->courses = Course::orderBy('title')->get();
    }

    public function loadLessons()
    {
        $query = Lesson::with('course')->orderBy('course_id')->orderBy('sort_order');

        if ($this->selectedCourseId) {
            $query->where('course_id', $this->selectedCourseId);
        }

        $this->lessons = $query->get();
    }

    public function filterByCourse($courseId)
    {
        $this->selectedCourseId = $courseId;
        $this->loadLessons();
    }

    public function createLesson()
    {
        $this->resetForm();
        $this->showCreateForm = true;
        $this->showEditForm = false;

        // Set default order
        if ($this->selectedCourseId) {
            $maxOrder = Lesson::where('course_id', $this->selectedCourseId)->max('sort_order');
            $this->sort_order = ($maxOrder ?? 0) + 1;
            $this->course_id = $this->selectedCourseId;
        }
    }

    public function storeLesson()
    {
        $this->validate();

        $lesson = new Lesson;
        $lesson->title = $this->title;
        $lesson->slug = Str::slug($this->title);
        $lesson->description = $this->description;
        $lesson->content = $this->content;
        $lesson->course_id = $this->course_id;
        $lesson->video_url = $this->video_url;
        $lesson->duration_minutes = $this->duration_minutes;
        $lesson->sort_order = $this->sort_order;
        $lesson->type = $this->type;
        $lesson->is_published = $this->is_published;
        $lesson->is_free = $this->is_free;

        $lesson->save();

        $this->showCreateForm = false;
        $this->loadLessons();
        $this->dispatch('lesson-saved', 'Lesson created successfully!');
    }

    public function editLesson($id)
    {
        $this->selectedLesson = Lesson::findOrFail($id);
        $this->title = $this->selectedLesson->title;
        $this->description = $this->selectedLesson->description;
        $this->content = $this->selectedLesson->content;
        $this->course_id = $this->selectedLesson->course_id;
        $this->video_url = $this->selectedLesson->video_url;
        $this->duration_minutes = $this->selectedLesson->duration_minutes;
        $this->sort_order = $this->selectedLesson->sort_order;
        $this->type = $this->selectedLesson->type;
        $this->is_published = $this->selectedLesson->is_published;
        $this->is_free = $this->selectedLesson->is_free;

        $this->showEditForm = true;
        $this->showCreateForm = false;
    }

    public function updateLesson()
    {
        $this->validate();

        $this->selectedLesson->title = $this->title;
        $this->selectedLesson->slug = Str::slug($this->title);
        $this->selectedLesson->description = $this->description;
        $this->selectedLesson->content = $this->content;
        $this->selectedLesson->course_id = $this->course_id;
        $this->selectedLesson->video_url = $this->video_url;
        $this->selectedLesson->duration_minutes = $this->duration_minutes;
        $this->selectedLesson->sort_order = $this->sort_order;
        $this->selectedLesson->type = $this->type;
        $this->selectedLesson->is_published = $this->is_published;
        $this->selectedLesson->is_free = $this->is_free;

        $this->selectedLesson->save();

        $this->showEditForm = false;
        $this->loadLessons();
        $this->dispatch('lesson-saved', 'Lesson updated successfully!');
    }

    public function deleteLesson($id)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->delete();
        $this->loadLessons();
        $this->dispatch('lesson-saved', 'Lesson deleted successfully!');
    }

    public function togglePublished($id)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->is_published = ! $lesson->is_published;
        $lesson->save();
        $this->loadLessons();
    }

    public function toggleFree($id)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->is_free = ! $lesson->is_free;
        $lesson->save();
        $this->loadLessons();
    }

    public function moveUp($id)
    {
        $lesson = Lesson::findOrFail($id);
        $previousLesson = Lesson::where('course_id', $lesson->course_id)
            ->where('sort_order', '<', $lesson->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();

        if ($previousLesson) {
            $tempOrder = $lesson->sort_order;
            $lesson->sort_order = $previousLesson->sort_order;
            $previousLesson->sort_order = $tempOrder;

            $lesson->save();
            $previousLesson->save();

            $this->loadLessons();
        }
    }

    public function moveDown($id)
    {
        $lesson = Lesson::findOrFail($id);
        $nextLesson = Lesson::where('course_id', $lesson->course_id)
            ->where('sort_order', '>', $lesson->sort_order)
            ->orderBy('sort_order')
            ->first();

        if ($nextLesson) {
            $tempOrder = $lesson->sort_order;
            $lesson->sort_order = $nextLesson->sort_order;
            $nextLesson->sort_order = $tempOrder;

            $lesson->save();
            $nextLesson->save();

            $this->loadLessons();
        }
    }

    public function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->content = '';
        $this->course_id = '';
        $this->video_url = '';
        $this->duration_minutes = 0;
        $this->sort_order = 0;
        $this->type = 'video';
        $this->is_published = false;
        $this->is_free = false;
        $this->selectedLesson = null;
    }

    public function cancel()
    {
        $this->showCreateForm = false;
        $this->showEditForm = false;
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.admin.lesson-manager')
            ->layout('layouts.admin')
            ->title('Lesson Management');
    }
}
