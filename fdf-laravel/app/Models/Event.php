<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'excerpt',
        'start_date',
        'end_date',
        'time',
        'location',
        'venue',
        'price',
        'registration_required',
        'registration_url',
        'image',
        'status',
        'is_virtual',
        'meeting_link',
        'max_attendees',
        'metadata',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'registration_required' => 'boolean',
        'is_virtual' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Scope to get only upcoming events.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now())
                    ->where('status', 'upcoming')
                    ->orderBy('start_date', 'asc');
    }

    /**
     * Scope to get only past events.
     */
    public function scopePast($query)
    {
        return $query->where('start_date', '<', now())
                    ->orderBy('start_date', 'desc');
    }

    /**
     * Scope to get only featured events.
     */
    public function scopeFeatured($query)
    {
        return $query->where('status', 'featured');
    }

    /**
     * Check if the event is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->start_date >= now();
    }

    /**
     * Check if the event is past.
     */
    public function isPast(): bool
    {
        return $this->start_date < now();
    }

    /**
     * Check if the event is happening today.
     */
    public function isToday(): bool
    {
        return $this->start_date->isToday();
    }

    /**
     * Get the formatted start date.
     */
    public function getFormattedStartDate(): string
    {
        return $this->start_date->format('F j, Y');
    }

    /**
     * Get the formatted date range.
     */
    public function getFormattedDateRange(): string
    {
        if ($this->end_date && $this->end_date->format('Y-m-d') !== $this->start_date->format('Y-m-d')) {
            return $this->start_date->format('F j') . ' - ' . $this->end_date->format('F j, Y');
        }
        
        return $this->getFormattedStartDate();
    }

    /**
     * Get the excerpt or generate one from description.
     */
    public function getExcerptAttribute($value): string
    {
        if ($value) {
            return $value;
        }
        
        // Generate excerpt from description (strip HTML, limit to 150 characters)
        $description = strip_tags($this->description);
        return strlen($description) > 150 ? substr($description, 0, 147) . '...' : $description;
    }

    /**
     * Get the display location (virtual or physical).
     */
    public function getDisplayLocation(): string
    {
        if ($this->is_virtual) {
            return 'Virtual Event';
        }
        
        return $this->location ?: 'Location TBD';
    }

    /**
     * Get the price display.
     */
    public function getDisplayPrice(): string
    {
        if (!$this->price) {
            return 'Free';
        }
        
        return $this->price;
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function hasAvailableCapacity(): bool
    {
        if ($this->max_attendees === null) {
            return true;
        }

        $registrationsCount = $this->registrations_count ?? $this->registrations()->count();

        return $registrationsCount < $this->max_attendees;
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }
}
