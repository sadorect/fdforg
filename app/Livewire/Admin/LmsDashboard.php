<?php

namespace App\Livewire\Admin;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;
use App\Support\AdminPermissions;

class LmsDashboard extends AdminComponent
{
    protected array $adminAbilities = [AdminPermissions::VIEW_LMS_DASHBOARD];

    public $courseCount = 0;

    public $publishedCourseCount = 0;

    public $lessonCount = 0;

    public $enrollmentCount = 0;

    public $activeEnrollmentCount = 0;

    public $completedEnrollmentCount = 0;

    public $completionRate = 0;

    public $totalLearners = 0;

    public $recentEnrollments;

    public $topCourses;

    public function mount(): void
    {
        $this->loadStats();
    }

    public function render()
    {
        return view('livewire.admin.lms-dashboard')
            ->layout('layouts.admin')
            ->title('LMS Dashboard');
    }

    private function loadStats(): void
    {
        $this->courseCount = Course::count();
        $this->publishedCourseCount = Course::where('status', 'published')->count();
        $this->lessonCount = Lesson::count();
        $this->enrollmentCount = Enrollment::count();
        $this->activeEnrollmentCount = Enrollment::where('status', 'active')->count();
        $this->completedEnrollmentCount = Enrollment::where('status', 'completed')->count();
        $this->totalLearners = User::where('is_admin', false)->count();

        $this->completionRate = $this->enrollmentCount > 0
            ? round(($this->completedEnrollmentCount / $this->enrollmentCount) * 100, 2)
            : 0;

        $this->recentEnrollments = Enrollment::with(['user', 'course'])
            ->latest('enrolled_at')
            ->take(8)
            ->get();

        $this->topCourses = Course::query()
            ->orderByDesc('enrollment_count')
            ->orderByDesc('rating')
            ->take(8)
            ->get();
    }
}
