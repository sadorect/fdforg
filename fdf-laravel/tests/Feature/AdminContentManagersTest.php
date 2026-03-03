<?php

namespace Tests\Feature;

use App\Livewire\Admin\BlogManager;
use App\Livewire\Admin\CategoryManager;
use App\Livewire\Admin\EventManager;
use App\Livewire\Admin\PageManager;
use App\Livewire\Admin\UserManager;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminContentManagersTest extends TestCase
{
    use RefreshDatabase;

    public function test_page_manager_can_create_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)
            ->test(PageManager::class)
            ->set('title', 'Admin Managed Page')
            ->set('slug', 'admin-managed-page')
            ->set('content', 'Content created by feature test.')
            ->set('meta_description', 'Test description')
            ->set('status', 'published')
            ->call('store')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('pages', [
            'title' => 'Admin Managed Page',
            'slug' => 'admin-managed-page',
            'status' => 'published',
        ]);
    }

    public function test_page_manager_can_edit_page_using_numeric_id(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $page = Page::create([
            'title' => 'Original Page',
            'slug' => 'original-page',
            'content' => 'Original content',
            'status' => 'draft',
        ]);

        Livewire::actingAs($admin)
            ->test(PageManager::class)
            ->call('edit', $page->id)
            ->assertSet('pageId', $page->id)
            ->set('title', 'Updated Page')
            ->set('slug', 'updated-page')
            ->set('content', 'Updated content')
            ->set('status', 'published')
            ->call('update')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'title' => 'Updated Page',
            'slug' => 'updated-page',
            'status' => 'published',
        ]);
    }

    public function test_event_manager_can_create_event(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Storage::fake('public');
        $image = UploadedFile::fake()->create('asl-workshop.png', 120, 'image/png');

        Livewire::actingAs($admin)
            ->test(EventManager::class)
            ->set('title', 'ASL Workshop')
            ->set('slug', 'asl-workshop')
            ->set('description', 'Hands-on ASL workshop.')
            ->set('excerpt', 'Short workshop summary.')
            ->set('start_date', now()->addDays(7)->format('Y-m-d\TH:i'))
            ->set('registration_required', true)
            ->set('status', 'upcoming')
            ->set('is_virtual', true)
            ->set('meeting_link', 'https://example.com/meet/asl-workshop')
            ->set('image', $image)
            ->call('save')
            ->assertHasNoErrors();

        $event = \App\Models\Event::where('slug', 'asl-workshop')->firstOrFail();

        $this->assertDatabaseHas('events', [
            'title' => 'ASL Workshop',
            'slug' => 'asl-workshop',
            'status' => 'upcoming',
            'is_virtual' => true,
            'registration_required' => true,
        ]);

        $this->assertNotNull($event->image);
        Storage::disk('public')->assertExists($event->image);
    }

    public function test_category_manager_can_create_and_toggle_category(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)
            ->test(CategoryManager::class)
            ->set('name', 'Accessibility')
            ->set('slug', 'accessibility')
            ->set('description', 'Accessibility-related content')
            ->set('type', 'blog')
            ->set('is_active', true)
            ->set('sort_order', 3)
            ->call('save')
            ->assertHasNoErrors();

        $category = Category::where('slug', 'accessibility')->firstOrFail();
        $this->assertTrue($category->is_active);

        Livewire::actingAs($admin)
            ->test(CategoryManager::class)
            ->call('toggleStatus', $category->id);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'is_active' => false,
        ]);
    }

    public function test_user_manager_can_create_user_and_toggle_admin(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Livewire::actingAs($admin)
            ->test(UserManager::class)
            ->set('name', 'Content Editor')
            ->set('email', 'editor@example.com')
            ->set('password', 'password123')
            ->set('bio', 'Editor bio')
            ->set('is_admin', false)
            ->set('email_verified', true)
            ->call('save')
            ->assertHasNoErrors();

        $editor = User::where('email', 'editor@example.com')->firstOrFail();
        $this->assertFalse((bool) $editor->is_admin);

        Livewire::actingAs($admin)
            ->test(UserManager::class)
            ->call('toggleAdmin', $editor->id);

        $this->assertDatabaseHas('users', [
            'id' => $editor->id,
            'is_admin' => true,
        ]);
    }

    public function test_blog_manager_can_create_post(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::create([
            'name' => 'News',
            'slug' => 'news',
            'type' => 'blog',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Livewire::actingAs($admin)
            ->test(BlogManager::class)
            ->set('title', 'Community Update')
            ->set('slug', 'community-update')
            ->set('excerpt', 'Important update for the community')
            ->set('content', 'Detailed content body.')
            ->set('category_id', $category->id)
            ->set('author_id', $admin->id)
            ->set('status', 'published')
            ->set('published_at', now()->format('Y-m-d\TH:i'))
            ->set('tags', ['update', 'community'])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('blog_posts', [
            'title' => 'Community Update',
            'slug' => 'community-update',
            'status' => 'published',
        ]);
    }

    public function test_blog_manager_can_edit_post_using_numeric_id(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::create([
            'name' => 'Community',
            'slug' => 'community',
            'type' => 'blog',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $post = BlogPost::create([
            'title' => 'Original Blog Title',
            'slug' => 'original-blog-title',
            'excerpt' => 'Original excerpt',
            'content' => 'Original content',
            'category_id' => $category->id,
            'author_id' => $admin->id,
            'status' => 'draft',
            'published_at' => null,
            'tags' => ['original'],
        ]);

        Livewire::actingAs($admin)
            ->test(BlogManager::class)
            ->call('edit', $post->id)
            ->assertSet('post_id', $post->id)
            ->set('title', 'Updated Blog Title')
            ->set('slug', 'updated-blog-title')
            ->set('excerpt', 'Updated excerpt')
            ->set('content', 'Updated content')
            ->set('status', 'published')
            ->set('published_at', now()->format('Y-m-d\TH:i'))
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('blog_posts', [
            'id' => $post->id,
            'title' => 'Updated Blog Title',
            'slug' => 'updated-blog-title',
            'status' => 'published',
        ]);
    }
}
