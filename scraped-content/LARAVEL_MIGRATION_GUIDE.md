# Friends of the Deaf Foundation - Laravel Migration Guide

## Scraped Content Summary

### Content Overview
- **Pages**: 6 pages successfully scraped with full content
- **Posts**: 0 posts (blog section may be empty or have different URLs)
- **Events**: 1 event scraped
- **Media**: 0 media files downloaded (images referenced in metadata)

### Scraped Pages
1. **Homepage** - Complete with hero sections, mission/vision, contact info
2. **About Us** - Foundation information and history
3. **Donate** - Donation page content
4. **FDF Academy** - Educational program information
5. **FDF Blog** - Blog page structure
6. **Main Foundation Page** - Additional foundation content

## Laravel Database Migration Strategy

### 1. Pages Table Migration

Based on the scraped content, create the following database structure:

```sql
-- Pages migration
CREATE TABLE pages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content LONGTEXT,
    excerpt TEXT,
    meta_title VARCHAR(255),
    meta_description TEXT,
    og_image VARCHAR(255),
    status ENUM('published', 'draft', 'archived') DEFAULT 'published',
    featured BOOLEAN DEFAULT FALSE,
    order_column INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_featured (featured),
    INDEX idx_order (order_column)
);
```

### 2. Events Table Migration

```sql
-- Events migration
CREATE TABLE events (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description LONGTEXT,
    date DATE,
    time TIME,
    location VARCHAR(255),
    venue VARCHAR(255),
    price DECIMAL(10, 2),
    registration_url VARCHAR(255),
    image VARCHAR(255),
    meta_title VARCHAR(255),
    meta_description TEXT,
    og_image VARCHAR(255),
    status ENUM('upcoming', 'past', 'cancelled') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_date (date)
);
```

## Laravel Seeder Implementation

### 1. Pages Seeder

Create `database/seeders/PagesSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PagesSeeder extends Seeder
{
    public function run()
    {
        // Load scraped pages data
        $pagesJson = file_get_contents(database_path('seeders/scraped-content/pages/homepage.json'));
        $homepage = json_decode($pagesJson, true);
        
        // Insert Homepage
        DB::table('pages')->insert([
            'title' => $homepage['title'],
            'slug' => 'home',
            'content' => $homepage['content'],
            'meta_title' => $homepage['metadata']['title'] ?? $homepage['title'],
            'meta_description' => $homepage['metadata']['og_description'] ?? '',
            'og_image' => $homepage['metadata']['og_image'] ?? '',
            'status' => 'published',
            'featured' => true,
            'order_column' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Insert other pages
        $otherPages = [
            'page_About-Us-Friends-of-The-Deaf-Foundation_1771951632.json' => 'about-us',
            'page_Donate-Friends-of-The-Deaf-Foundation_1771951631.json' => 'donate',
            'page_FDF-Academy-Friends-of-The-Deaf-Foundation_1771951630.json' => 'fdf-academy',
            'page_FDF-Blog-Friends-of-The-Deaf-Foundation_1771951633.json' => 'blog',
            'page_Friends-of-The-Deaf-Foundation-Bridging-the-communication-gap_1771951629.json' => 'foundation',
        ];
        
        $order = 2;
        foreach ($otherPages as $filename => $slug) {
            $pageJson = file_get_contents(database_path("seeders/scraped-content/pages/{$filename}"));
            $page = json_decode($pageJson, true);
            
            if ($page) {
                DB::table('pages')->insert([
                    'title' => $page['title'],
                    'slug' => $slug,
                    'content' => $page['content'],
                    'meta_title' => $page['metadata']['title'] ?? $page['title'],
                    'meta_description' => $page['metadata']['og_description'] ?? '',
                    'og_image' => $page['metadata']['og_image'] ?? '',
                    'status' => 'published',
                    'featured' => false,
                    'order_column' => $order,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $order++;
            }
        }
    }
}
```

### 2. Events Seeder

Create `database/seeders/EventsSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventsSeeder extends Seeder
{
    public function run()
    {
        // Load scraped events data
        $eventsJson = file_get_contents(database_path('seeders/scraped-content/events/all_events.json'));
        $events = json_decode($eventsJson, true);
        
        foreach ($events as $event) {
            DB::table('events')->insert([
                'title' => $event['title'],
                'slug' => Str::slug($event['title']),
                'description' => $event['description'] ?? '',
                'date' => $event['date'] ?? null,
                'time' => $event['time'] ?? null,
                'location' => $event['location'] ?? '',
                'venue' => $event['venue'] ?? '',
                'price' => $event['price'] ?? 0.00,
                'registration_url' => $event['registration_url'] ?? '',
                'image' => $event['metadata']['og_image'] ?? '',
                'meta_title' => $event['metadata']['title'] ?? $event['title'],
                'meta_description' => $event['metadata']['og_description'] ?? '',
                'og_image' => $event['metadata']['og_image'] ?? '',
                'status' => 'past', // Since it's a 2021 event
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
```

### 3. Navigation Menu Seeder

Create `database/seeders/NavigationSeeder.php` based on scraped navigation:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NavigationSeeder extends Seeder
{
    public function run()
    {
        $navigationItems = [
            ['title' => 'Home', 'url' => '/', 'order' => 1, 'parent_id' => null],
            ['title' => 'About Us', 'url' => '/about-us', 'order' => 2, 'parent_id' => null],
            ['title' => 'FDF Training', 'url' => '/training', 'order' => 3, 'parent_id' => null],
            ['title' => 'FDF Academy', 'url' => '/fdf-academy', 'order' => 4, 'parent_id' => null],
            ['title' => 'FDF Blog', 'url' => '/blog', 'order' => 5, 'parent_id' => null],
            ['title' => 'Donate Now', 'url' => '/donate', 'order' => 6, 'parent_id' => null],
        ];
        
        foreach ($navigationItems as $item) {
            DB::table('navigation_menu_items')->insert($item);
        }
    }
}
```

## Model Implementation

### 1. Page Model

Create `app/Models/Page.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Page extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'meta_title',
        'meta_description',
        'og_image',
        'status',
        'featured',
        'order_column',
    ];

    protected $casts = [
        'featured' => 'boolean',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order_column');
    }
}
```

### 2. Event Model

Create `app/Models/Event.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'date',
        'time',
        'location',
        'venue',
        'price',
        'registration_url',
        'image',
        'meta_title',
        'meta_description',
        'og_image',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'time',
        'price' => 'decimal:2',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming')->where('date', '>=', Carbon::today());
    }

    public function scopePast($query)
    {
        return $query->where('status', 'past')->orWhere('date', '<', Carbon::today());
    }

    public function getFormattedDateAttribute()
    {
        return $this->date ? $this->date->format('F j, Y') : '';
    }

    public function getFormattedTimeAttribute()
    {
        return $this->time ? $this->time->format('g:i A') : '';
    }
}
```

## Controller Implementation

### 1. Page Controller

Create `app/Http/Controllers/PageController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->published()->firstOrFail();
        
        return view('pages.show', compact('page'));
    }

    public function home()
    {
        $homepage = Page::where('slug', 'home')->published()->firstOrFail();
        $featuredPages = Page::published()->featured()->ordered()->take(3)->get();
        
        return view('home', compact('homepage', 'featuredPages'));
    }
}
```

### 2. Event Controller

Create `app/Http/Controllers/EventController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $upcomingEvents = Event::upcoming()->orderBy('date')->get();
        $pastEvents = Event::past()->orderBy('date', 'desc')->take(5)->get();
        
        return view('events.index', compact('upcomingEvents', 'pastEvents'));
    }

    public function show($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        
        return view('events.show', compact('event'));
    }
}
```

## Routes Configuration

Update `routes/web.php`:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\EventController;

// Homepage
Route::get('/', [PageController::class, 'home'])->name('home');

// Pages
Route::get('/{slug}', [PageController::class, 'show'])->name('pages.show');

// Events
Route::prefix('events')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('events.index');
    Route::get('/{slug}', [EventController::class, 'show'])->name('events.show');
});
```

## Blade Templates

### 1. Home Page Template

Create `resources/views/home.blade.php`:

```blade
@extends('layouts.app')

@section('content')
<main class="main-content">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            {!! $homepage->content !!}
        </div>
    </section>

    <!-- Featured Pages -->
    @if($featuredPages->count())
        <section class="featured-pages py-5">
            <div class="container">
                <h2 class="text-center mb-4">Learn More About Us</h2>
                <div class="row">
                    @foreach($featuredPages as $page)
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h3 class="card-title">{{ $page->title }}</h3>
                                    <p class="card-text">{{ Str::limit(strip_tags($page->content), 150) }}</p>
                                    <a href="{{ route('pages.show', $page->slug) }}" class="btn btn-primary">
                                        Read More
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</main>
@endsection
```

### 2. Page Template

Create `resources/views/pages/show.blade.php`:

```blade
@extends('layouts.app')

@section('content')
<main class="main-content">
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <article class="page-content">
                    <h1 class="page-title mb-4">{{ $page->title }}</h1>
                    <div class="page-body">
                        {!! $page->content !!}
                    </div>
                </article>
            </div>
        </div>
    </div>
</main>
@endsection
```

## Media Management

### Image Storage Setup

1. Create symbolic link:
```bash
php artisan storage:link
```

2. Download images from metadata and store in `storage/app/public/media/`

3. Create a media download script:

```php
// Create a script to download referenced images
<?php
// scripts/download_media.php

$jsonFiles = [
    'scraped-content/pages/homepage.json',
    // Add other page files
];

foreach ($jsonFiles as $file) {
    $data = json_decode(file_get_contents($file), true);
    
    if (!empty($data['metadata']['og_image'])) {
        $imageUrl = $data['metadata']['og_image'];
        $imageName = basename(parse_url($imageUrl, PHP_URL_PATH));
        $imagePath = 'storage/app/public/media/' . $imageName;
        
        // Download and save image
        file_put_contents($imagePath, file_get_contents($imageUrl));
    }
}
```

## Migration Steps

### 1. Database Setup

```bash
# Create migrations
php artisan make:migration create_pages_table
php artisan make:migration create_events_table
php artisan make:migration create_navigation_menu_items_table

# Run migrations
php artisan migrate
```

### 2. Copy Scraped Content

```bash
# Copy scraped content to database seeders directory
cp -r scraped-content/* database/seeders/scraped-content/
```

### 3. Run Seeders

```bash
# Run all seeders
php artisan db:seed --class=PagesSeeder
php artisan db:seed --class=EventsSeeder
php artisan db:seed --class=NavigationSeeder
```

### 4. Verify Content

Check the database to ensure all content was imported correctly:

```sql
-- Check pages
SELECT COUNT(*) FROM pages; -- Should be 6
SELECT title, slug FROM pages ORDER BY order_column;

-- Check events
SELECT COUNT(*) FROM events; -- Should be 1
SELECT title, slug FROM events;
```

## Next Steps

### 1. Content Review
- Review all imported content for accuracy
- Update any broken internal links
- Optimize images for web performance

### 2. SEO Optimization
- Generate XML sitemaps
- Set up proper meta tags
- Configure redirects for old WordPress URLs

### 3. Performance Optimization
- Implement caching strategies
- Optimize images and media files
- Set up CDN for static assets

### 4. Testing
- Test all page functionality
- Verify navigation works correctly
- Check mobile responsiveness
- Test form submissions if any

### 5. Launch Preparation
- Set up monitoring and analytics
- Configure backup strategies
- Prepare deployment scripts

## Content Mapping Summary

| Original WordPress URL | Laravel Route | Page Type | Status |
|------------------------|---------------|-----------|---------|
| / (homepage) | / | Homepage | ✅ Migrated |
| /about-us/ | /about-us | Page | ✅ Migrated |
| /donate/ | /donate | Page | ✅ Migrated |
| /fdf-academy/ | /fdf-academy | Page | ✅ Migrated |
| /fdf-blog/ | /blog | Page | ✅ Migrated |
| /event/2021-international-day-of-sign-language/ | /events/2021-international-day-of-sign-language | Event | ✅ Migrated |

## File Structure

```
database/
├── migrations/
│   ├── 2024_01_01_000001_create_pages_table.php
│   ├── 2024_01_01_000002_create_events_table.php
│   └── 2024_01_01_000003_create_navigation_menu_items_table.php
├── seeders/
│   ├── PagesSeeder.php
│   ├── EventsSeeder.php
│   ├── NavigationSeeder.php
│   └── scraped-content/
│       ├── pages/
│       ├── posts/
│       └── events/
```

This migration guide provides a complete roadmap for transferring the scraped WordPress content to the new Laravel application while maintaining SEO value and content integrity.