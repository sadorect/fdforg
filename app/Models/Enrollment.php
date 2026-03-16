<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'enrolled_at',
        'completed_at',
        'progress_percentage',
        'last_accessed_at',
        'certificate_url',
        'paid_amount',
        'currency_code',
        'payment_status',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'progress_percentage' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByCourse(Builder $query, int $courseId): Builder
    {
        return $query->where('course_id', $courseId);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePendingPayment(Builder $query): Builder
    {
        return $query->where('payment_status', 'pending');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isPaymentPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function getFormattedProgressAttribute(): string
    {
        return number_format($this->progress_percentage, 1) . '%';
    }

    public function getFormattedPaidAmountAttribute(): string
    {
        return $this->currency_symbol . number_format($this->paid_amount, 2);
    }

    public function getCurrencySymbolAttribute(): string
    {
        return Course::SUPPORTED_CURRENCIES[$this->currency_code] ?? ($this->currency_code ?: 'USD');
    }

    public function getTimeSpentAttribute(): string
    {
        $totalSeconds = $this->lessonProgress()->sum('watch_time_seconds');
        
        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;
        
        if ($hours > 0) {
            return sprintf('%dh %dm %ds', $hours, $minutes, $seconds);
        } elseif ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $seconds);
        } else {
            return sprintf('%ds', $seconds);
        }
    }

    public function getDaysEnrolledAttribute(): int
    {
        return $this->enrolled_at?->diffInDays(now()) ?? 0;
    }

    public function getCompletionRateAttribute(): float
    {
        $totalLessons = $this->course->publishedLessons()->count();
        
        if ($totalLessons === 0) {
            return 0;
        }
        
        $completedLessons = $this->lessonProgress()
                                  ->where('is_completed', true)
                                  ->count();
        
        return round(($completedLessons / $totalLessons) * 100, 2);
    }

    public function getLastLessonAccessedAttribute(): ?string
    {
        $lastProgress = $this->lessonProgress()
                             ->where('last_accessed_at', '!=', null)
                             ->orderBy('last_accessed_at', 'desc')
                             ->first();
        
        return $lastProgress?->last_accessed_at?->diffForHumans();
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'progress_percentage' => 100.00,
        ]);

        // Generate certificate if enabled
        if ($this->course->is_certificate_enabled) {
            $this->generateCertificate();
        }
    }

    public function markAsActive(): void
    {
        $this->update([
            'status' => 'active',
            'enrolled_at' => $this->enrolled_at ?? now(),
        ]);
    }

    public function suspend(): void
    {
        $this->update([
            'status' => 'suspended',
        ]);
    }

    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
        ]);
    }

    public function markAsPaid(float $amount = null): void
    {
        $this->update([
            'payment_status' => 'paid',
            'paid_amount' => $amount ?? $this->course->price,
            'currency_code' => $this->course->currency_code,
        ]);
    }

    public function updateProgress(): void
    {
        $totalLessons = $this->course->publishedLessons()->count();
        
        if ($totalLessons === 0) {
            return;
        }
        
        $completedLessons = $this->lessonProgress()
                                  ->where('is_completed', true)
                                  ->count();
        
        $progressPercentage = round(($completedLessons / $totalLessons) * 100, 2);
        
        $this->update([
            'progress_percentage' => $progressPercentage,
            'last_accessed_at' => now(),
        ]);

        // Auto-complete if 100%
        if ($progressPercentage >= 100 && !$this->isCompleted()) {
            $this->markAsCompleted();
        }
    }

    public function canAccessLesson(Lesson $lesson): bool
    {
        if (!in_array($this->status, ['active', 'completed'], true)) {
            return false;
        }

        if ($lesson->course_id !== $this->course_id) {
            return false;
        }

        if (!$lesson->is_published) {
            return false;
        }

        if ($lesson->is_free) {
            return true;
        }

        return $this->isPaid();
    }

    private function generateCertificate(): void
    {
        // Generate certificate URL logic here
        // For now, we'll create a placeholder URL
        $certificateUrl = 'certificates/' . $this->course->slug . '-' . $this->user->id . '.pdf';
        
        $this->update([
            'certificate_url' => $certificateUrl,
        ]);
    }

    public function getNextLesson(): ?Lesson
    {
        $lessons = $this->course->publishedLessons()->get();
        
        foreach ($lessons as $lesson) {
            if (!$lesson->isUserCompleted($this->user)) {
                return $lesson;
            }
        }
        
        return null;
    }

    public function getCurrentLesson(): ?Lesson
    {
        $lastAccessedProgress = $this->lessonProgress()
                                     ->where('last_accessed_at', '!=', null)
                                     ->orderBy('last_accessed_at', 'desc')
                                     ->first();
        
        if ($lastAccessedProgress) {
            return $lastAccessedProgress->lesson;
        }
        
        // Return first lesson if no progress yet
        return $this->course->publishedLessons()->first();
    }
}
