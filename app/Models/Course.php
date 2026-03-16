<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Course extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'featured_image',
        'intro_video_url',
        'difficulty_level',
        'duration_minutes',
        'price',
        'currency_code',
        'status',
        'instructor_id',
        'category_id',
        'max_students',
        'start_date',
        'end_date',
        'prerequisites',
        'learning_outcomes',
        'is_certificate_enabled',
        'is_featured',
        'enrollment_count',
        'rating',
        'review_count',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'price' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_certificate_enabled' => 'boolean',
        'is_featured' => 'boolean',
        'enrollment_count' => 'integer',
        'rating' => 'decimal:2',
        'review_count' => 'integer',
    ];

    public const SUPPORTED_CURRENCIES = [
        'USD' => '$',
        'NGN' => '₦',
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

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order');
    }

    public function publishedLessons(): HasMany
    {
        return $this->hasMany(Lesson::class)
                    ->where('is_published', true)
                    ->orderBy('sort_order');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function activeEnrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class)->where('status', 'active');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeFree(Builder $query): Builder
    {
        return $query->where('price', 0);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('price', '>', 0);
    }

    public function scopeByDifficulty(Builder $query, string $level): Builder
    {
        return $query->where('difficulty_level', $level);
    }

    public function scopeByCategory(Builder $query, string $categorySlug): Builder
    {
        return $query->whereHas('category', function ($q) use ($categorySlug) {
            $q->where('slug', $categorySlug);
        });
    }

    public function scopeByInstructor(Builder $query, int $instructorId): Builder
    {
        return $query->where('instructor_id', $instructorId);
    }

    public function getFormattedPriceAttribute(): string
    {
        return $this->price > 0
            ? $this->currencySymbol . number_format($this->price, 2)
            : 'Free';
    }

    public function getCurrencySymbolAttribute(): string
    {
        return self::SUPPORTED_CURRENCIES[$this->currency_code] ?? ($this->currency_code ?: 'USD');
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

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->featured_image) {
            return asset('storage/' . $this->featured_image);
        }
        
        return 'https://picsum.photos/seed/' . $this->slug . '/800/450.jpg';
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isFree(): bool
    {
        return $this->price == 0;
    }

    public function hasCapacity(): bool
    {
        if ($this->max_students === null) {
            return true;
        }
        
        return $this->activeEnrollments()->count() < $this->max_students;
    }

    public function isUserEnrolled(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        
        return $this->enrollments()
                    ->where('user_id', $user->id)
                    ->whereIn('status', ['active', 'completed'])
                    ->exists();
    }

    public function getUserEnrollment(?User $user): ?Enrollment
    {
        if (!$user) {
            return null;
        }
        
        return $this->enrollments()
                    ->where('user_id', $user->id)
                    ->first();
    }

    public function getUserProgress(?User $user): float
    {
        if (!$user) {
            return 0;
        }
        
        $enrollment = $this->getUserEnrollment($user);
        
        return $enrollment ? $enrollment->progress_percentage : 0;
    }

    public function incrementEnrollmentCount(): void
    {
        $this->increment('enrollment_count');
    }

    public function updateRating(): void
    {
        // This would typically calculate from reviews/reviews table
        // For now, we'll keep the existing rating
    }

    public function getPrerequisitesListAttribute(): array
    {
        return $this->prerequisites ?? [];
    }

    public function getPrerequisitesAttribute($value): array
    {
        return $this->normalizeListAttribute($value);
    }

    public function setPrerequisitesAttribute($value): void
    {
        $normalized = $this->normalizeListAttribute($value);

        $this->attributes['prerequisites'] = $normalized === []
            ? null
            : json_encode($normalized);
    }

    public function getLearningOutcomesListAttribute(): array
    {
        return $this->learning_outcomes ?? [];
    }

    public function getLearningOutcomesAttribute($value): array
    {
        return $this->normalizeListAttribute($value);
    }

    public function setLearningOutcomesAttribute($value): void
    {
        $normalized = $this->normalizeListAttribute($value);

        $this->attributes['learning_outcomes'] = $normalized === []
            ? null
            : json_encode($normalized);
    }

    private function normalizeListAttribute($value): array
    {
        if (is_array($value)) {
            return $this->cleanList($value);
        }

        if (! is_string($value)) {
            return [];
        }

        $value = trim($value);

        if ($value === '' || strtolower($value) === 'null') {
            return [];
        }

        $decoded = json_decode($value, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            if (is_array($decoded)) {
                return $this->cleanList($decoded);
            }

            if (is_string($decoded)) {
                return $this->normalizeListAttribute($decoded);
            }
        }

        $lines = preg_split('/\r\n|\r|\n/', $value) ?: [];

        if (count(array_filter($lines, static fn ($line) => trim($line) !== '')) > 1) {
            return $this->cleanList($lines);
        }

        return [$value];
    }

    private function cleanList(array $items): array
    {
        return array_values(array_filter(array_map(static function ($item) {
            if (! is_scalar($item) && $item !== null) {
                return null;
            }

            $item = trim((string) $item);

            return $item === '' ? null : $item;
        }, $items)));
    }
}
