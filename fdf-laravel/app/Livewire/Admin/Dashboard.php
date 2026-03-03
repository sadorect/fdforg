<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Page;
use App\Models\Event;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\LessonProgress;

class Dashboard extends Component
{
    public $totalPages;
    public $totalEvents;
    public $totalBlogPosts;
    public $totalCategories;
    public $totalCourses;
    public $totalLessons;
    public $totalUsers;
    public $totalEnrollments;
    public $totalLessonProgress;

    public $recentPages;
    public $recentEvents;
    public $recentBlogPosts;
    public $recentCourses;
    public $recentEnrollments;

    public function mount()
    {
        $this->loadStatistics();
        $this->loadRecentData();
    }

    public function loadStatistics()
    {
        $this->totalPages = Page::count();
        $this->totalEvents = Event::count();
        $this->totalBlogPosts = BlogPost::count();
        $this->totalCategories = Category::count();
        $this->totalCourses = Course::count();
        $this->totalLessons = Lesson::count();
        $this->totalUsers = User::count();
        $this->totalEnrollments = Enrollment::count();
        $this->totalLessonProgress = LessonProgress::count();
    }

    public function loadRecentData()
    {
        $this->recentPages = Page::latest()->take(5)->get();
        $this->recentEvents = Event::latest()->take(5)->get();
        $this->recentBlogPosts = BlogPost::latest()->take(5)->get();
        $this->recentCourses = Course::latest()->take(5)->get();
        $this->recentEnrollments = Enrollment::latest()->take(5)->get();
    }

    public function render()
    {
        return view('livewire.admin.dashboard')
            ->layout('layouts.admin')
            ->title('Admin Dashboard');
    }
}