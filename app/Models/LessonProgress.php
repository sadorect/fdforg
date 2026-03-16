<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lesson_id',
        'enrollment_id',
        'is_completed',
        'watch_time_seconds',
        'started_at',
        'completed_at',
        'last_accessed_at',
        'completion_percentage',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'watch_time_seconds' => 'integer',
        'completion_percentage' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_accessed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function isStarted(): bool
    {
        return $this->started_at !== null;
    }

    public function isCompleted(): bool
    {
        return $this->is_completed;
    }

    public function getFormattedWatchTimeAttribute(): string
    {
        $hours = floor($this->watch_time_seconds / 3600);
        $minutes = floor(($this->watch_time_seconds % 3600) / 60);
        $seconds = $this->watch_time_seconds % 60;
        
        if ($hours > 0) {
            return sprintf('%dh %dm %ds', $hours, $minutes, $seconds);
        } elseif ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $seconds);
        } else {
            return sprintf('%ds', $seconds);
        }
    }

    public function getCompletionPercentageAttribute(): int
    {
        return $this->attributes['completion_percentage'] ?? 0;
    }

    public function getTimeRemainingAttribute(): ?int
    {
        if (!$this->lesson || !$this->lesson->duration_minutes) {
            return null;
        }

        $totalSeconds = $this->lesson->duration_minutes * 60;
        $remainingSeconds = max(0, $totalSeconds - $this->watch_time_seconds);
        
        return $remainingSeconds;
    }

    public function getFormattedTimeRemainingAttribute(): ?string
    {
        $remaining = $this->getTimeRemainingAttribute();
        
        if ($remaining === null) {
            return null;
        }

        $hours = floor($remaining / 3600);
        $minutes = floor(($remaining % 3600) / 60);
        $seconds = $remaining % 60;
        
        if ($hours > 0) {
            return sprintf('%dh %dm remaining', $hours, $minutes);
        } elseif ($minutes > 0) {
            return sprintf('%dm remaining', $minutes);
        } else {
            return sprintf('%ds remaining', $seconds);
        }
    }

    public function markAsStarted(): void
    {
        if (!$this->isStarted()) {
            $this->update([
                'started_at' => now(),
                'last_accessed_at' => now(),
                'completion_percentage' => 0,
            ]);
        } else {
            $this->update([
                'last_accessed_at' => now(),
            ]);
        }
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'is_completed' => true,
            'completion_percentage' => 100,
            'completed_at' => now(),
            'last_accessed_at' => now(),
        ]);

        // Update course progress through enrollment
        if ($this->enrollment) {
            $this->enrollment->updateProgress();
        }
    }

    public function updateProgress(int $percentage, int $watchTime = null): void
    {
        $updateData = [
            'completion_percentage' => min(100, max(0, $percentage)),
            'last_accessed_at' => now(),
        ];

        if ($watchTime !== null) {
            $updateData['watch_time_seconds'] = $watchTime;
        }

        // Set started_at if not already set
        if (!$this->started_at) {
            $updateData['started_at'] = now();
        }

        $this->update($updateData);

        // Auto-complete if 100%
        if ($percentage >= 100 && !$this->is_completed) {
            $this->markAsCompleted();
        }

        // Update course progress through enrollment
        if ($this->enrollment) {
            $this->enrollment->updateProgress();
        }
    }

    public function incrementWatchTime(int $seconds): void
    {
        $newWatchTime = $this->watch_time_seconds + $seconds;
        
        // Calculate completion percentage based on lesson duration
        if ($this->lesson && $this->lesson->duration_minutes > 0) {
            $totalSeconds = $this->lesson->duration_minutes * 60;
            $percentage = min(100, round(($newWatchTime / $totalSeconds) * 100));
            
            $this->updateProgress($percentage, $newWatchTime);
        } else {
            // Just update watch time if no duration is set
            $this->update([
                'watch_time_seconds' => $newWatchTime,
                'last_accessed_at' => now(),
                'started_at' => $this->started_at ?? now(),
            ]);
        }
    }

    public function reset(): void
    {
        $this->update([
            'is_completed' => false,
            'completion_percentage' => 0,
            'watch_time_seconds' => 0,
            'started_at' => null,
            'completed_at' => null,
            'last_accessed_at' => now(),
        ]);

        // Update course progress through enrollment
        if ($this->enrollment) {
            $this->enrollment->updateProgress();
        }
    }

    public function getProgressStatusAttribute(): string
    {
        if ($this->is_completed) {
            return 'completed';
        } elseif ($this->is_started()) {
            return 'in_progress';
        } else {
            return 'not_started';
        }
    }

    public function getProgressStatusColorAttribute(): string
    {
        return match($this->progress_status) {
            'completed' => 'green',
            'in_progress' => 'blue',
            'not_started' => 'gray',
            default => 'gray',
        };
    }

    public function getProgressLabelAttribute(): string
    {
        return match($this->progress_status) {
            'completed' => 'Completed',
            'in_progress' => 'In Progress',
            'not_started' => 'Not Started',
            default => 'Unknown',
        };
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function scopeNotCompleted($query)
    {
        return $query->where('is_completed', false);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByLesson($query, int $lessonId)
    {
        return $query->where('lesson_id', $lessonId);
    }

    public function scopeByEnrollment($query, int $enrollmentId)
    {
        return $query->where('enrollment_id', $enrollmentId);
    }
}