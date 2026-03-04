<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image_path',
        'image_paths',
        'type',
        'event_name',
        'captured_at',
        'is_featured',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'image_paths' => 'array',
        'captured_at' => 'date',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function getImageUrlAttribute(): string
    {
        $path = $this->primary_image_path;

        return $path ? asset('storage/' . $path) : '';
    }

    public function getImageUrlsAttribute(): array
    {
        return collect($this->normalized_image_paths)
            ->map(fn (string $path) => asset('storage/' . $path))
            ->all();
    }

    public function getPrimaryImagePathAttribute(): ?string
    {
        return $this->normalized_image_paths[0] ?? null;
    }

    public function getNormalizedImagePathsAttribute(): array
    {
        $paths = is_array($this->image_paths) ? $this->image_paths : [];

        if (empty($paths) && !empty($this->image_path)) {
            $paths = [$this->image_path];
        }

        return array_values(array_filter($paths, fn ($value) => is_string($value) && $value !== ''));
    }
}
