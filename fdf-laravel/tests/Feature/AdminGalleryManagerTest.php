<?php

namespace Tests\Feature;

use App\Livewire\Admin\GalleryManager;
use App\Models\GalleryItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminGalleryManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_gallery_management_page(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin)
            ->get('/admin/gallery')
            ->assertOk()
            ->assertSee('Gallery Management');
    }

    public function test_admin_can_create_gallery_item(): void
    {
        Storage::fake('public');
        $admin = $this->createAdminUser();
        $firstImage = UploadedFile::fake()->create('gallery-one.jpg', 128, 'image/jpeg');
        $secondImage = UploadedFile::fake()->create('gallery-two.jpg', 128, 'image/jpeg');

        Livewire::actingAs($admin)
            ->test(GalleryManager::class)
            ->set('title', 'Field Outreach Day')
            ->set('slug', 'field-outreach-day')
            ->set('description', 'Photo from outreach event.')
            ->set('type', 'event')
            ->set('event_name', 'Outreach Day')
            ->set('status', 'published')
            ->set('sort_order', 3)
            ->set('is_featured', true)
            ->set('images', [$firstImage, $secondImage])
            ->call('save')
            ->assertHasNoErrors();

        $item = GalleryItem::where('slug', 'field-outreach-day')->first();

        $this->assertNotNull($item);
        $this->assertSame('Field Outreach Day', $item->title);
        $this->assertTrue((bool) $item->is_featured);
        $this->assertCount(2, $item->normalized_image_paths);
        Storage::disk('public')->assertExists($item->normalized_image_paths[0]);
        Storage::disk('public')->assertExists($item->normalized_image_paths[1]);
    }
}
