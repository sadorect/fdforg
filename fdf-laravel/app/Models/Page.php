<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'meta_title',
        'meta_description',
        'meta_image',
        'status',
        'order',
        'metadata',
        'navigation',
        'sections',
    ];

    protected $casts = [
        'metadata' => 'array',
        'navigation' => 'array',
        'sections' => 'array',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Scope to get only published pages.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope to get pages in order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('title', 'asc');
    }

    /**
     * Get the meta title or fall back to the page title.
     */
    public function getMetaTitleAttribute($value): string
    {
        return $value ?: $this->title;
    }

    /**
     * Get the excerpt or generate one from content.
     */
    public function getExcerptAttribute($value): string
    {
        if ($value) {
            return $value;
        }
        
        // Generate excerpt from content (strip HTML, limit to 150 characters)
        $content = strip_tags($this->content);
        return strlen($content) > 150 ? substr($content, 0, 147) . '...' : $content;
    }
}