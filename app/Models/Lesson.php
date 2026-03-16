<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Lesson extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'video_url',
        'video_thumbnail',
        'type',
        'duration_minutes',
        'sort_order',
        'is_free',
        'is_published',
        'course_id',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'sort_order' => 'integer',
        'is_free' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function userProgress(?User $user): ?LessonProgress
    {
        if (!$user) {
            return null;
        }

        return $this->lessonProgress()
                    ->where('user_id', $user->id)
                    ->first();
    }

    public function isUserCompleted(?User $user): bool
    {
        $progress = $this->userProgress($user);
        
        return $progress ? $progress->is_completed : false;
    }

    public function getUserCompletionPercentage(?User $user): int
    {
        $progress = $this->userProgress($user);
        
        return $progress ? $progress->completion_percentage : 0;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeFree(Builder $query): Builder
    {
        return $query->where('is_free', true);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }

    public function getFormattedDurationAttribute(): string
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;
        
        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        
        return $minutes . 'm';
    }

    public function getVideoEmbedUrlAttribute(): ?string
    {
        if (!$this->video_url) {
            return null;
        }

        // Handle YouTube URLs
        if (str_contains($this->video_url, 'youtube.com') || str_contains($this->video_url, 'youtu.be')) {
            $videoId = $this->extractYoutubeVideoId($this->video_url);
            return $videoId ? "https://www.youtube.com/embed/{$videoId}" : null;
        }

        // Handle Vimeo URLs
        if (str_contains($this->video_url, 'vimeo.com')) {
            $videoId = $this->extractVimeoVideoId($this->video_url);
            return $videoId ? "https://player.vimeo.com/video/{$videoId}" : null;
        }

        // Return original URL if it's already an embed URL
        if (str_contains($this->video_url, 'embed')) {
            return $this->video_url;
        }

        return $this->video_url;
    }

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->video_thumbnail) {
            return $this->video_thumbnail;
        }

        if ($this->video_url) {
            // Try to extract YouTube thumbnail
            $youtubeId = $this->extractYoutubeVideoId($this->video_url);
            if ($youtubeId) {
                return "https://img.youtube.com/vi/{$youtubeId}/hqdefault.jpg";
            }
        }

        return 'https://picsum.photos/seed/' . $this->slug . '/800/450.jpg';
    }

    public function isAccessibleByUser(?User $user): bool
    {
        if (!$this->is_published) {
            return false;
        }

        if ($this->is_free) {
            return true;
        }

        if (!$user) {
            return false;
        }

        $enrollment = $this->course->getUserEnrollment($user);

        return $enrollment !== null
            && $enrollment->canAccessLesson($this)
            && $enrollment->payment_status === 'paid';
    }

    public function markAsCompleted(User $user): void
    {
        $enrollment = $this->course->getUserEnrollment($user);
        
        if (!$enrollment) {
            return;
        }

        $progress = $this->lessonProgress()
                        ->firstOrCreate([
                            'user_id' => $user->id,
                            'enrollment_id' => $enrollment->id,
                        ]);

        $progress->update([
            'is_completed' => true,
            'completion_percentage' => 100,
            'completed_at' => now(),
            'last_accessed_at' => now(),
        ]);

        // Update course progress
        $this->updateCourseProgress($user);
    }

    public function updateProgress(User $user, int $percentage, int $watchTime = 0): void
    {
        $enrollment = $this->course->getUserEnrollment($user);
        
        if (!$enrollment) {
            return;
        }

        $progress = $this->lessonProgress()
                        ->firstOrCreate([
                            'user_id' => $user->id,
                            'enrollment_id' => $enrollment->id,
                        ]);

        $progress->update([
            'completion_percentage' => min(100, $percentage),
            'watch_time_seconds' => $watchTime,
            'last_accessed_at' => now(),
            'started_at' => $progress->started_at ?? now(),
        ]);

        // Mark as completed if 100%
        if ($percentage >= 100 && !$progress->is_completed) {
            $progress->update([
                'is_completed' => true,
                'completed_at' => now(),
            ]);
        }

        // Update course progress
        $this->updateCourseProgress($user);
    }

    private function updateCourseProgress(User $user): void
    {
        $enrollment = $this->course->getUserEnrollment($user);
        
        if (!$enrollment) {
            return;
        }

        $totalLessons = $this->course->publishedLessons()->count();
        $completedLessons = $this->course->publishedLessons()
                                        ->whereHas('lessonProgress', function ($query) use ($user) {
                                            $query->where('user_id', $user->id)
                                                  ->where('is_completed', true);
                                        })
                                        ->count();

        $progressPercentage = $totalLessons > 0 
            ? round(($completedLessons / $totalLessons) * 100, 2) 
            : 0;

        $enrollment->update([
            'progress_percentage' => $progressPercentage,
            'last_accessed_at' => now(),
        ]);

        // Mark course as completed if 100%
        if ($progressPercentage >= 100 && $enrollment->status !== 'completed') {
            $enrollment->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }
    }

    private function extractYoutubeVideoId(string $url): ?string
    {
        $pattern = '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function extractVimeoVideoId(string $url): ?string
    {
        $pattern = '/vimeo\.com\/(\d+)/';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
