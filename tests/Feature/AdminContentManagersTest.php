<?php

namespace Tests\Feature;

use App\Livewire\Admin\BlogManager;
use App\Livewire\Admin\CategoryManager;
use App\Livewire\Admin\EventManager;
use App\Livewire\Admin\PageManager;
use App\Livewire\Admin\UserManager;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Event;
use App\Models\Page;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Support\AdminPermissions;
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
        $admin = $this->createAdminUser();

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
        $admin = $this->createAdminUser();
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

    public function test_page_manager_opens_preview_before_publishing(): void
    {
        $admin = $this->createAdminUser();

        Livewire::actingAs($admin)
            ->test(PageManager::class)
            ->set('title', 'Preview Ready Page')
            ->set('slug', 'preview-ready-page')
            ->set('content', '<h2>Preview content</h2><p>This page should be reviewed first.</p>')
            ->set('status', 'published')
            ->call('submit')
            ->assertSet('showPreview', true);

        $this->assertDatabaseMissing('pages', [
            'slug' => 'preview-ready-page',
        ]);
    }

    public function test_page_manager_can_publish_after_preview_confirmation(): void
    {
        $admin = $this->createAdminUser();

        Livewire::actingAs($admin)
            ->test(PageManager::class)
            ->set('title', 'Confirmed Preview Page')
            ->set('slug', 'confirmed-preview-page')
            ->set('content', '<p>Confirmed for publishing.</p>')
            ->set('meta_description', 'Preview confirmation test')
            ->set('status', 'published')
            ->call('submit')
            ->assertSet('showPreview', true)
            ->call('confirmPreviewAction')
            ->assertHasNoErrors()
            ->assertSet('showPreview', false)
            ->assertSet('showForm', false);

        $this->assertDatabaseHas('pages', [
            'title' => 'Confirmed Preview Page',
            'slug' => 'confirmed-preview-page',
            'status' => 'published',
        ]);
    }

    public function test_page_manager_can_save_structured_homepage_content(): void
    {
        $admin = $this->createAdminUser();
        Storage::fake('public');

        Livewire::actingAs($admin)
            ->test(PageManager::class)
            ->set('title', 'Homepage')
            ->set('slug', 'home')
            ->set('content', 'Lead homepage narrative.')
            ->set('meta_description', 'Homepage metadata')
            ->set('featured_image', UploadedFile::fake()->image('home-hero.jpg'))
            ->set('homeSections.landing.headline', 'A homepage headline shaped in admin')
            ->set('homeSections.identity.mission_body', 'Mission body from admin page manager')
            ->set('homeSections.services.items.0.title', 'Homepage service card')
            ->set('homeSections.trust.visible', false)
            ->set('homeSections.trust.story_visible', false)
            ->set('homeSections.trust.partners_visible', false)
            ->call('store')
            ->assertHasNoErrors();

        $page = Page::where('slug', 'home')->firstOrFail();

        $this->assertSame('A homepage headline shaped in admin', data_get($page->sections, 'landing.headline'));
        $this->assertSame('Mission body from admin page manager', data_get($page->sections, 'identity.mission_body'));
        $this->assertSame('Homepage service card', data_get($page->sections, 'services.items.0.title'));
        $this->assertFalse((bool) data_get($page->sections, 'trust.visible'));
        $this->assertFalse((bool) data_get($page->sections, 'trust.story_visible'));
        $this->assertFalse((bool) data_get($page->sections, 'trust.partners_visible'));
        $this->assertNotNull($page->meta_image);
        Storage::disk('public')->assertExists($page->meta_image);
    }

    public function test_page_manager_can_save_homepage_partner_logo_entries(): void
    {
        $admin = $this->createAdminUser();
        Storage::fake('public');

        Livewire::actingAs($admin)
            ->test(PageManager::class)
            ->set('title', 'Homepage')
            ->set('slug', 'home')
            ->set('content', 'Lead homepage narrative.')
            ->set('status', 'published')
            ->call('addTrustPartner')
            ->set('homeSections.trust.partners_visible', true)
            ->set('homeSections.trust.partners_title', 'Partners and supporters')
            ->set('homeSections.trust.partners.0.name', 'Partner One')
            ->set('homeSections.trust.partners.0.website_url', 'https://partner-one.example')
            ->set('partnerLogoUploads.0', UploadedFile::fake()->image('partner-one.png'))
            ->call('store')
            ->assertHasNoErrors();

        $page = Page::where('slug', 'home')->firstOrFail();
        $logoPath = data_get($page->sections, 'trust.partners.0.logo_path');

        $this->assertSame('Partner One', data_get($page->sections, 'trust.partners.0.name'));
        $this->assertSame('https://partner-one.example', data_get($page->sections, 'trust.partners.0.website_url'));
        $this->assertNotNull($logoPath);
        Storage::disk('public')->assertExists($logoPath);
    }

    public function test_page_manager_can_save_structured_about_page_content(): void
    {
        $admin = $this->createAdminUser();
        Storage::fake('public');

        Livewire::actingAs($admin)
            ->test(PageManager::class)
            ->set('title', 'About')
            ->set('slug', 'about')
            ->set('content', '<p>Our detailed organizational story.</p>')
            ->set('meta_description', 'About page metadata')
            ->set('featured_image', UploadedFile::fake()->image('about-hero.jpg'))
            ->set('aboutSections.hero.headline', 'About page headline from admin')
            ->set('aboutSections.story.title', 'Why this work matters')
            ->set('aboutSections.identity.values_body', 'Values text managed in admin')
            ->set('aboutSections.commitments.items.0.title', 'Community-led support')
            ->call('store')
            ->assertHasNoErrors();

        $page = Page::where('slug', 'about')->firstOrFail();

        $this->assertSame('About page headline from admin', data_get($page->sections, 'hero.headline'));
        $this->assertSame('Why this work matters', data_get($page->sections, 'story.title'));
        $this->assertSame('Values text managed in admin', data_get($page->sections, 'identity.values_body'));
        $this->assertSame('Community-led support', data_get($page->sections, 'commitments.items.0.title'));
        $this->assertNotNull($page->meta_image);
        Storage::disk('public')->assertExists($page->meta_image);
    }

    public function test_page_manager_can_save_structured_programs_page_content(): void
    {
        $admin = $this->createAdminUser();
        Storage::fake('public');

        Livewire::actingAs($admin)
            ->test(PageManager::class)
            ->set('title', 'Programs')
            ->set('slug', 'programs')
            ->set('content', '<p>Detailed programs story.</p>')
            ->set('meta_description', 'Programs page metadata')
            ->set('featured_image', UploadedFile::fake()->image('programs-hero.jpg'))
            ->set('programsSections.hero.headline', 'Programs headline from admin')
            ->set('programsSections.story.title', 'Programs story title')
            ->set('programsSections.pillars.items.0.title', 'Learning support')
            ->set('programsSections.audiences.items.0.title', 'Deaf children and youth')
            ->set('programsSections.outcomes.quote_author', 'Programs team')
            ->call('store')
            ->assertHasNoErrors();

        $page = Page::where('slug', 'programs')->firstOrFail();

        $this->assertSame('Programs headline from admin', data_get($page->sections, 'hero.headline'));
        $this->assertSame('Programs story title', data_get($page->sections, 'story.title'));
        $this->assertSame('Learning support', data_get($page->sections, 'pillars.items.0.title'));
        $this->assertSame('Deaf children and youth', data_get($page->sections, 'audiences.items.0.title'));
        $this->assertSame('Programs team', data_get($page->sections, 'outcomes.quote_author'));
        $this->assertNotNull($page->meta_image);
        Storage::disk('public')->assertExists($page->meta_image);
    }

    public function test_page_manager_can_save_structured_donations_page_content(): void
    {
        $admin = $this->createAdminUser();
        Storage::fake('public');

        Livewire::actingAs($admin)
            ->test(PageManager::class)
            ->set('title', 'Donations')
            ->set('slug', 'donations')
            ->set('content', '<p>Detailed donation appeal.</p>')
            ->set('meta_description', 'Donations page metadata')
            ->set('featured_image', UploadedFile::fake()->image('donations-hero.jpg'))
            ->set('donationsSections.hero.headline', 'Support the mission headline')
            ->set('donationsSections.bank.accounts.0.account_number', '0123456789')
            ->set('donationsSections.bank.accounts.1.account_number', 'USD-000123456')
            ->set('donationsSections.acknowledgement.email_address', 'donations@example.org')
            ->set('donationsSections.impact.items.0.title', 'Learning access support')
            ->call('store')
            ->assertHasNoErrors();

        $page = Page::where('slug', 'donations')->firstOrFail();

        $this->assertSame('Support the mission headline', data_get($page->sections, 'hero.headline'));
        $this->assertSame('0123456789', data_get($page->sections, 'bank.accounts.0.account_number'));
        $this->assertSame('USD-000123456', data_get($page->sections, 'bank.accounts.1.account_number'));
        $this->assertSame('donations@example.org', data_get($page->sections, 'acknowledgement.email_address'));
        $this->assertSame('Learning access support', data_get($page->sections, 'impact.items.0.title'));
        $this->assertNotNull($page->meta_image);
        Storage::disk('public')->assertExists($page->meta_image);
    }

    public function test_page_manager_can_save_structured_contact_page_content(): void
    {
        $admin = $this->createAdminUser();
        Storage::fake('public');

        Livewire::actingAs($admin)
            ->test(PageManager::class)
            ->set('title', 'Contact')
            ->set('slug', 'contact')
            ->set('content', '<p>Detailed contact introduction.</p>')
            ->set('meta_description', 'Contact page metadata')
            ->set('featured_image', UploadedFile::fake()->image('contact-hero.jpg'))
            ->set('contactSections.hero.headline', 'Reach our team quickly')
            ->set('contactSections.intro.title', 'Why people contact us')
            ->set('contactSections.pathways.items.0.title', 'General support')
            ->set('contactSections.form.title', 'Send us a message')
            ->call('store')
            ->assertHasNoErrors();

        $page = Page::where('slug', 'contact')->firstOrFail();

        $this->assertSame('Reach our team quickly', data_get($page->sections, 'hero.headline'));
        $this->assertSame('Why people contact us', data_get($page->sections, 'intro.title'));
        $this->assertSame('General support', data_get($page->sections, 'pathways.items.0.title'));
        $this->assertSame('Send us a message', data_get($page->sections, 'form.title'));
        $this->assertNotNull($page->meta_image);
        Storage::disk('public')->assertExists($page->meta_image);
    }

    public function test_event_manager_can_create_event(): void
    {
        $admin = $this->createAdminUser();
        Storage::fake('public');
        $image = UploadedFile::fake()->create('asl-workshop.png', 120, 'image/png');

        Livewire::actingAs($admin)
            ->test(EventManager::class)
            ->set('title', 'ASL Workshop')
            ->set('slug', 'asl-workshop')
            ->set('description', '<h2>Hands-on ASL workshop</h2><p>Rich event details for attendees.</p>')
            ->set('excerpt', 'Short workshop summary.')
            ->set('start_date', now()->addDays(7)->format('Y-m-d\TH:i'))
            ->set('registration_required', true)
            ->set('status', 'upcoming')
            ->set('is_virtual', true)
            ->set('meeting_link', 'https://example.com/meet/asl-workshop')
            ->set('image', $image)
            ->call('save')
            ->assertHasNoErrors();

        $event = Event::where('slug', 'asl-workshop')->firstOrFail();

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
        $admin = $this->createAdminUser();

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
        $admin = $this->createAdminUser();

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

    public function test_scoped_user_manager_cannot_grant_admin_access_or_assign_roles(): void
    {
        $scopedAdmin = User::factory()->create(['is_admin' => true]);
        $permission = Permission::create([
            'name' => 'Manage Users',
            'slug' => AdminPermissions::MANAGE_USERS,
            'description' => 'Manage users permission for tests.',
        ]);
        $userManagerRole = Role::create([
            'name' => 'User Manager',
            'slug' => 'user-manager',
            'description' => 'Scoped user management role.',
        ]);
        $userManagerRole->permissions()->attach($permission->id);
        $scopedAdmin->roles()->attach($userManagerRole->id);

        $assignableRole = Role::create([
            'name' => 'Assignable Role',
            'slug' => 'assignable-role',
            'description' => 'Role that should not be assignable by scoped user managers.',
        ]);

        Livewire::actingAs($scopedAdmin)
            ->test(UserManager::class)
            ->set('name', 'Scoped Editor')
            ->set('email', 'scoped-editor@example.com')
            ->set('password', 'password123')
            ->set('bio', 'Editor bio')
            ->set('is_admin', true)
            ->set('role_ids', [$assignableRole->id])
            ->call('save')
            ->assertHasNoErrors();

        $createdUser = User::where('email', 'scoped-editor@example.com')->firstOrFail();

        $this->assertFalse((bool) $createdUser->is_admin);
        $this->assertSame(0, $createdUser->roles()->count());

        Livewire::actingAs($scopedAdmin)
            ->test(UserManager::class)
            ->call('toggleAdmin', $createdUser->id);

        $this->assertFalse((bool) $createdUser->fresh()->is_admin);
    }

    public function test_blog_manager_can_create_post(): void
    {
        $admin = $this->createAdminUser();
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
            ->set('content', '<h2>Detailed content body</h2><p>Formatted content for the community update.</p>')
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
        $admin = $this->createAdminUser();
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
