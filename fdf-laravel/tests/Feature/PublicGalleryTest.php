<?php

namespace Tests\Feature;

use App\Models\GalleryItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_page_lists_only_published_items(): void
    {
        GalleryItem::create([
            'title' => 'Community Sign Workshop',
            'slug' => 'community-sign-workshop',
            'description' => 'Local workshop session.',
            'image_path' => 'gallery/workshop.jpg',
            'type' => 'activity',
            'status' => 'published',
            'sort_order' => 1,
        ]);

        GalleryItem::create([
            'title' => 'Draft Event Photo',
            'slug' => 'draft-event-photo',
            'description' => 'Not yet published.',
            'image_path' => 'gallery/draft.jpg',
            'type' => 'event',
            'status' => 'draft',
            'sort_order' => 2,
        ]);

        $this->get('/gallery')
            ->assertOk()
            ->assertSee('Images that show the people, programs, and public moments behind the mission.')
            ->assertSee('Hero slider')
            ->assertSee('Community Sign Workshop')
            ->assertDontSee('Draft Event Photo');
    }

    public function test_gallery_page_can_filter_items_by_type(): void
    {
        GalleryItem::create([
            'title' => 'Activity Snapshot',
            'slug' => 'activity-snapshot',
            'description' => 'Activity photo.',
            'image_path' => 'gallery/activity.jpg',
            'type' => 'activity',
            'status' => 'published',
            'sort_order' => 1,
        ]);

        GalleryItem::create([
            'title' => 'Event Snapshot',
            'slug' => 'event-snapshot',
            'description' => 'Event photo.',
            'image_path' => 'gallery/event.jpg',
            'type' => 'event',
            'status' => 'published',
            'sort_order' => 1,
        ]);

        $this->get('/gallery?type=event')
            ->assertOk()
            ->assertSee('Showing Event collections')
            ->assertSee('Event Snapshot')
            ->assertDontSee('Activity Snapshot');
    }
}
