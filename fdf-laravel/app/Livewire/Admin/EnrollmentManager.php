<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\User;

class EnrollmentManager extends Component
{
    public $enrollments;
    public $courses;
    public $users;
    public $selectedEnrollment = null;
    public $showCreateForm = false;
    public $showEditForm = false;
    public $selectedCourseId = null;
    public $selectedUserId = null;
    public $selectedStatus = null;
    
    // Form fields
    public $course_id;
    public $user_id;
    public $enrolled_at;
    public $completed_at;
    public $status = 'active';
    public $progress_percentage = 0;
    public $payment_status = 'pending';
    public $paid_amount = 0;
    public $currency_code = 'USD';
    
    protected $rules = [
        'course_id' => 'required|exists:courses,id',
        'user_id' => 'required|exists:users,id',
        'enrolled_at' => 'required|date',
        'completed_at' => 'nullable|date',
        'status' => 'required|in:active,completed,cancelled,suspended',
        'progress_percentage' => 'required|numeric|min:0|max:100',
        'payment_status' => 'required|in:pending,paid,refunded',
        'paid_amount' => 'required|numeric|min:0',
        'currency_code' => 'required|in:USD,NGN',
    ];

    public function mount()
    {
        $this->loadEnrollments();
        $this->courses = Course::orderBy('title')->get();
        $this->users = User::orderBy('name')->get();
        $this->enrolled_at = now()->format('Y-m-d\TH:i');
    }

    public function loadEnrollments()
    {
        $query = Enrollment::with(['course', 'user'])->orderBy('enrolled_at', 'desc');
        
        if ($this->selectedCourseId) {
            $query->where('course_id', $this->selectedCourseId);
        }
        
        if ($this->selectedUserId) {
            $query->where('user_id', $this->selectedUserId);
        }
        
        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }
        
        $this->enrollments = $query->get();
    }

    public function filterByCourse($courseId)
    {
        $this->selectedCourseId = $courseId;
        $this->loadEnrollments();
    }

    public function filterByUser($userId)
    {
        $this->selectedUserId = $userId;
        $this->loadEnrollments();
    }

    public function filterByStatus($status)
    {
        $this->selectedStatus = $status;
        $this->loadEnrollments();
    }

    public function createEnrollment()
    {
        $this->resetForm();
        $this->showCreateForm = true;
        $this->showEditForm = false;
    }

    public function storeEnrollment()
    {
        $this->validate();

        // Check if user is already enrolled
        $existingEnrollment = Enrollment::where('course_id', $this->course_id)
            ->where('user_id', $this->user_id)
            ->first();
            
        if ($existingEnrollment) {
            $this->addError('duplicate', 'User is already enrolled in this course');
            return;
        }

        $enrollment = new Enrollment();
        $enrollment->course_id = $this->course_id;
        $enrollment->user_id = $this->user_id;
        $enrollment->enrolled_at = $this->enrolled_at;
        $enrollment->completed_at = $this->completed_at;
        $enrollment->status = $this->status;
        $enrollment->progress_percentage = $this->progress_percentage;
        $enrollment->payment_status = $this->payment_status;
        $enrollment->paid_amount = $this->payment_status === 'paid' && $this->paid_amount == 0
            ? $this->courses->firstWhere('id', (int)$this->course_id)?->price ?? 0
            : $this->paid_amount;
        $enrollment->currency_code = $this->currency_code;
        
        $enrollment->save();

        $this->showCreateForm = false;
        $this->loadEnrollments();
        $this->dispatch('enrollment-saved', 'Enrollment created successfully!');
    }

    public function editEnrollment($id)
    {
        $this->selectedEnrollment = Enrollment::findOrFail($id);
        $this->course_id = $this->selectedEnrollment->course_id;
        $this->user_id = $this->selectedEnrollment->user_id;
        $this->enrolled_at = $this->selectedEnrollment->enrolled_at?->format('Y-m-d\TH:i');
        $this->completed_at = $this->selectedEnrollment->completed_at?->format('Y-m-d\TH:i');
        $this->status = $this->selectedEnrollment->status;
        $this->progress_percentage = $this->selectedEnrollment->progress_percentage;
        $this->payment_status = $this->selectedEnrollment->payment_status;
        $this->paid_amount = $this->selectedEnrollment->paid_amount;
        $this->currency_code = $this->selectedEnrollment->currency_code;
        
        $this->showEditForm = true;
        $this->showCreateForm = false;
    }

    public function updateEnrollment()
    {
        $this->validate();

        $this->selectedEnrollment->course_id = $this->course_id;
        $this->selectedEnrollment->user_id = $this->user_id;
        $this->selectedEnrollment->enrolled_at = $this->enrolled_at;
        $this->selectedEnrollment->completed_at = $this->completed_at;
        $this->selectedEnrollment->status = $this->status;
        $this->selectedEnrollment->progress_percentage = $this->progress_percentage;
        $this->selectedEnrollment->payment_status = $this->payment_status;
        $this->selectedEnrollment->paid_amount = $this->paid_amount;
        $this->selectedEnrollment->currency_code = $this->currency_code;
        
        $this->selectedEnrollment->save();

        $this->showEditForm = false;
        $this->loadEnrollments();
        $this->dispatch('enrollment-saved', 'Enrollment updated successfully!');
    }

    public function deleteEnrollment($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->delete();
        $this->loadEnrollments();
        $this->dispatch('enrollment-saved', 'Enrollment deleted successfully!');
    }

    public function updateProgress($id, $progress)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->progress_percentage = $progress;
        
        // Auto-complete if progress is 100%
        if ($progress >= 100) {
            $enrollment->status = 'completed';
            $enrollment->completed_at = now();
        }
        
        $enrollment->save();
        $this->loadEnrollments();
        $this->dispatch('enrollment-saved', 'Progress updated successfully!');
    }

    public function markAsCompleted($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->status = 'completed';
        $enrollment->progress_percentage = 100;
        $enrollment->completed_at = now();
        $enrollment->save();
        $this->loadEnrollments();
        $this->dispatch('enrollment-saved', 'Enrollment marked as completed!');
    }

    public function cancelEnrollment($id)
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->status = 'cancelled';
        $enrollment->save();
        $this->loadEnrollments();
        $this->dispatch('enrollment-saved', 'Enrollment cancelled!');
    }

    public function resetForm()
    {
        $this->course_id = '';
        $this->user_id = '';
        $this->enrolled_at = now()->format('Y-m-d\TH:i');
        $this->completed_at = '';
        $this->status = 'active';
        $this->progress_percentage = 0;
        $this->payment_status = 'pending';
        $this->paid_amount = 0;
        $this->currency_code = 'USD';
        $this->selectedEnrollment = null;
    }

    public function updatedCourseId($value): void
    {
        $course = $this->courses->firstWhere('id', (int) $value);

        if ($course) {
            $this->currency_code = $course->currency_code;

            if ($this->payment_status === 'paid' && (float) $this->paid_amount === 0.0) {
                $this->paid_amount = $course->price;
            }
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
        return view('livewire.admin.enrollment-manager')
            ->layout('layouts.admin')
            ->title('Enrollment Management');
    }
}
