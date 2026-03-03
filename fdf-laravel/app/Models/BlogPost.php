<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'youtube_video_id',
        'video_thumbnail',
        'status',
        'published_at',
        'author_id',
        'category_id',
        'tags',
        'views',
        'is_featured',
        'allow_comments',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'tags' => 'array',
        'views' => 'integer',
        'is_featured' => 'boolean',
        'allow_comments' => 'boolean',
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

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
                    ->where('published_at', '<=', now());
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory(Builder $query, string $categorySlug): Builder
    {
        return $query->whereHas('category', function ($q) use ($categorySlug) {
            $q->where('slug', $categorySlug);
        });
    }

    public function scopeByTag(Builder $query, string $tag): Builder
    {
        return $query->whereJsonContains('tags', $tag);
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('published_at', 'desc');
    }

    public function scopePopular(Builder $query): Builder
    {
        return $query->orderBy('views', 'desc');
    }

    public function getExcerptAttribute($value): string
    {
        return $value ?: Str::limit(strip_tags($this->content), 150);
    }

    public function getTagListAttribute(): array
    {
        $tags = $this->tags;

        if (is_array($tags)) {
            return array_values(array_filter(array_map('trim', $tags)));
        }

        if (is_string($tags) && $tags !== '') {
            $decoded = json_decode($tags, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return array_values(array_filter(array_map('trim', $decoded)));
            }

            return array_values(array_filter(array_map('trim', explode(',', $tags))));
        }

        return [];
    }

    public function getReadingTimeAttribute(): int
    {
        $wordsPerMinute = 200;
        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, ceil($wordCount / $wordsPerMinute));
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published' && $this->published_at?->isPast();
    }

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->video_thumbnail) {
            return $this->video_thumbnail;
        }
        
        if ($this->youtube_video_id) {
            return "https://img.youtube.com/vi/{$this->youtube_video_id}/hqdefault.jpg";
        }
        
        if ($this->featured_image) {
            return asset('storage/' . $this->featured_image);
        }
        
        return 'https://picsum.photos/seed/' . $this->slug . '/800/450.jpg';
    }
}
