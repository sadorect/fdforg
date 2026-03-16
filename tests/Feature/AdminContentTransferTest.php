<?php

namespace Tests\Feature;

use App\Livewire\Admin\ContentTransferManager;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Course;
use App\Models\EmailTemplate;
use App\Models\Event;
use App\Models\GalleryItem;
use App\Models\HeroSlide;
use App\Models\Lesson;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Services\ContentTransferService;
use App\Support\AdminPermissions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminContentTransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_pages_admin_can_open_content_transfer_page(): void
    {
        $admin = $this->createScopedAdminUser([AdminPermissions::MANAGE_PAGES]);

        $this->actingAs($admin)
            ->get(route('admin.content-transfer'))
            ->assertOk()
            ->assertSee('Content Transfer')
            ->assertSee('Import Page Package')
            ->assertDontSee('Import Site Settings Package');
    }

    public function test_page_export_package_includes_page_assets(): void
    {
        Storage::fake('public');
        $admin = $this->createScopedAdminUser([AdminPermissions::MANAGE_PAGES]);

        Storage::disk('public')->put('pages/home-hero.jpg', 'home-image-content');
        Storage::disk('public')->put('pages/partners/partner-one.png', 'partner-image-content');

        $page = Page::create([
            'title' => 'Homepage',
            'slug' => 'home',
            'content' => '<p>Homepage content</p>',
            'status' => 'published',
            'meta_image' => 'pages/home-hero.jpg',
            'sections' => [
                'trust' => [
                    'partners' => [
                        [
                            'name' => 'Partner One',
                            'website_url' => 'https://partner.example',
                            'logo_path' => 'pages/partners/partner-one.png',
                        ],
                    ],
                ],
            ],
        ]);

        $response = $this->actingAs($admin)->get(route('admin.content-transfer.page.export', $page));

        $response->assertOk();
        $payload = json_decode($response->streamedContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('page', $payload['type']);
        $this->assertSame('home', $payload['page']['slug']);
        $this->assertSame(
            base64_encode('home-image-content'),
            data_get($payload, 'page.assets.meta_image.content_base64')
        );
        $this->assertSame(
            base64_encode('partner-image-content'),
            data_get($payload, 'page.assets.partner_logos.0.asset.content_base64')
        );
    }

    public function test_page_transfer_import_creates_page_and_restores_assets(): void
    {
        Storage::fake('public');
        $admin = $this->createScopedAdminUser([AdminPermissions::MANAGE_PAGES]);

        $package = [
            'type' => 'page',
            'version' => 1,
            'page' => [
                'slug' => 'about',
                'title' => 'Imported About Page',
                'content' => '<p>Imported about page content.</p>',
                'excerpt' => 'Imported excerpt',
                'meta_title' => 'Imported Meta Title',
                'meta_description' => 'Imported meta description',
                'meta_image' => null,
                'status' => 'published',
                'show_media_sidebar' => true,
                'order' => 2,
                'metadata' => ['theme' => 'editorial'],
                'navigation' => ['visible' => true],
                'sections' => [
                    'trust' => [
                        'partners' => [
                            [
                                'name' => 'Partner One',
                                'website_url' => 'https://partner.example',
                                'logo_path' => null,
                            ],
                        ],
                    ],
                ],
                'assets' => [
                    'meta_image' => [
                        'type' => 'storage',
                        'filename' => 'about-hero.jpg',
                        'mime_type' => 'image/jpeg',
                        'content_base64' => base64_encode('about-hero-image'),
                    ],
                    'partner_logos' => [
                        [
                            'index' => 0,
                            'asset' => [
                                'type' => 'storage',
                                'filename' => 'partner-one.png',
                                'mime_type' => 'image/png',
                                'content_base64' => base64_encode('partner-logo-image'),
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $upload = UploadedFile::fake()->createWithContent(
            'about-page-transfer.json',
            json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        Livewire::actingAs($admin)
            ->test(ContentTransferManager::class)
            ->set('pageTransferUpload', $upload)
            ->call('importPageTransfer')
            ->assertHasNoErrors();

        $page = Page::where('slug', 'about')->firstOrFail();

        $this->assertSame('Imported About Page', $page->title);
        $this->assertSame('Imported Meta Title', $page->meta_title);
        $this->assertTrue((bool) $page->show_media_sidebar);
        $this->assertNotNull($page->meta_image);
        Storage::disk('public')->assertExists($page->meta_image);
        Storage::disk('public')->assertExists(data_get($page->sections, 'trust.partners.0.logo_path'));
    }

    public function test_site_settings_export_package_includes_branding_assets(): void
    {
        Storage::fake('public');
        $admin = $this->createScopedAdminUser([AdminPermissions::MANAGE_SITE_SETTINGS]);

        Storage::disk('public')->put('settings/site-logo.png', 'logo-content');
        Storage::disk('public')->put('settings/site-favicon.png', 'favicon-content');

        SiteSetting::setValue('site_name', 'FDF Nigeria');
        SiteSetting::setValue('site_logo_path', 'settings/site-logo.png');
        SiteSetting::setValue('site_favicon_path', 'settings/site-favicon.png');
        SiteSetting::setValue('social_x_bearer_token', 'secret-token-value');

        $response = $this->actingAs($admin)->get(route('admin.content-transfer.site-settings.export'));

        $response->assertOk();
        $payload = json_decode($response->streamedContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('site-settings', $payload['type']);
        $this->assertTrue((bool) data_get($payload, 'meta.contains_sensitive_values'));
        $this->assertSame('FDF Nigeria', data_get($payload, 'settings.site_name'));
        $this->assertSame(base64_encode('logo-content'), data_get($payload, 'assets.site_logo_path.content_base64'));
        $this->assertSame(base64_encode('favicon-content'), data_get($payload, 'assets.site_favicon_path.content_base64'));
    }

    public function test_site_settings_transfer_import_updates_values_and_assets(): void
    {
        Storage::fake('public');
        $admin = $this->createScopedAdminUser([AdminPermissions::MANAGE_SITE_SETTINGS]);

        $package = [
            'type' => 'site-settings',
            'version' => 1,
            'settings' => [
                'site_name' => 'Friends of the Deaf Foundation',
                'footer_email' => 'support@example.org',
                'footer_phone' => '+2348000000000',
                'site_logo_path' => null,
                'site_favicon_path' => null,
                'social_x_bearer_token' => 'secure-bearer-token',
            ],
            'assets' => [
                'site_logo_path' => [
                    'type' => 'storage',
                    'filename' => 'logo.png',
                    'mime_type' => 'image/png',
                    'content_base64' => base64_encode('logo-binary'),
                ],
                'site_favicon_path' => [
                    'type' => 'storage',
                    'filename' => 'favicon.png',
                    'mime_type' => 'image/png',
                    'content_base64' => base64_encode('favicon-binary'),
                ],
            ],
        ];

        $upload = UploadedFile::fake()->createWithContent(
            'site-settings-transfer.json',
            json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        Livewire::actingAs($admin)
            ->test(ContentTransferManager::class)
            ->set('siteSettingsTransferUpload', $upload)
            ->call('importSiteSettingsTransfer')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('site_settings', ['key' => 'site_name', 'value' => 'Friends of the Deaf Foundation']);
        $this->assertDatabaseHas('site_settings', ['key' => 'footer_email', 'value' => 'support@example.org']);
        $this->assertDatabaseHas('site_settings', ['key' => 'social_x_bearer_token', 'value' => 'secure-bearer-token']);

        $logoPath = SiteSetting::getValue('site_logo_path', '');
        $faviconPath = SiteSetting::getValue('site_favicon_path', '');

        $this->assertNotSame('', $logoPath);
        $this->assertNotSame('', $faviconPath);
        Storage::disk('public')->assertExists($logoPath);
        Storage::disk('public')->assertExists($faviconPath);
    }

    public function test_full_site_bundle_export_includes_nested_packages_and_records_log(): void
    {
        Storage::fake('public');
        $admin = $this->createScopedAdminUser([
            AdminPermissions::MANAGE_PAGES,
            AdminPermissions::MANAGE_SITE_SETTINGS,
            AdminPermissions::MANAGE_BLOG,
            AdminPermissions::MANAGE_EVENTS,
            AdminPermissions::MANAGE_GALLERY,
            AdminPermissions::MANAGE_COURSES,
            AdminPermissions::MANAGE_LESSONS,
            AdminPermissions::MANAGE_HERO_SLIDES,
            AdminPermissions::MANAGE_EMAIL_TEMPLATES,
        ]);

        $blogCategory = Category::create([
            'name' => 'News',
            'slug' => 'news',
            'type' => 'blog',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $courseCategory = Category::create([
            'name' => 'Sign Language',
            'slug' => 'sign-language',
            'type' => 'course',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        BlogPost::create([
            'title' => 'Community Update',
            'slug' => 'community-update',
            'content' => '<p>Community blog post.</p>',
            'status' => 'published',
            'published_at' => now(),
            'author_id' => $admin->id,
            'category_id' => $blogCategory->id,
        ]);

        Event::create([
            'title' => 'Community Event',
            'slug' => 'community-event',
            'description' => '<p>Event description.</p>',
            'start_date' => now()->addDay(),
            'status' => 'upcoming',
        ]);

        Storage::disk('public')->put('gallery/test.png', 'gallery-image');
        GalleryItem::create([
            'title' => 'Gallery Highlight',
            'slug' => 'gallery-highlight',
            'image_path' => 'gallery/test.png',
            'image_paths' => ['gallery/test.png'],
            'type' => 'activity',
            'status' => 'published',
        ]);

        Course::create([
            'title' => 'Intro to ASL',
            'slug' => 'intro-to-asl',
            'description' => 'Course description.',
            'status' => 'published',
            'instructor_id' => $admin->id,
            'category_id' => $courseCategory->id,
        ]);

        $course = Course::where('slug', 'intro-to-asl')->firstOrFail();
        Lesson::create([
            'title' => 'Lesson One',
            'slug' => 'lesson-one',
            'content' => 'Lesson content',
            'course_id' => $course->id,
        ]);

        HeroSlide::create([
            'title' => 'Hero',
            'content' => 'Hero content',
        ]);

        EmailTemplate::create([
            'key' => 'test_template',
            'name' => 'Test Template',
            'subject' => 'Hello',
            'body' => 'Body',
        ]);

        SiteSetting::setValue('site_name', 'FDF');

        $response = $this->actingAs($admin)->get(
            route('admin.content-transfer.bundle.export', ContentTransferService::BUNDLE_SITE)
        );

        $response->assertOk();
        $payload = json_decode($response->streamedContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame(ContentTransferService::BUNDLE_SITE, $payload['type']);
        $this->assertArrayHasKey('pages', $payload['packages']);
        $this->assertArrayHasKey('site_settings', $payload['packages']);
        $this->assertArrayHasKey('blog', $payload['packages']);
        $this->assertArrayHasKey('events', $payload['packages']);
        $this->assertArrayHasKey('gallery', $payload['packages']);
        $this->assertArrayHasKey('learning', $payload['packages']);
        $this->assertArrayHasKey('hero_slides', $payload['packages']);
        $this->assertArrayHasKey('email_templates', $payload['packages']);
        $this->assertDatabaseHas('content_transfer_logs', [
            'action' => 'export',
            'package_type' => ContentTransferService::BUNDLE_SITE,
        ]);
    }

    public function test_learning_bundle_import_creates_records_and_logs_activity(): void
    {
        Storage::fake('public');
        $admin = $this->createScopedAdminUser([
            AdminPermissions::MANAGE_COURSES,
            AdminPermissions::MANAGE_LESSONS,
        ]);

        $package = [
            'type' => ContentTransferService::BUNDLE_LEARNING,
            'version' => 1,
            'categories' => [
                [
                    'name' => 'Sign Language',
                    'slug' => 'sign-language',
                    'type' => 'course',
                    'is_active' => true,
                    'sort_order' => 1,
                ],
            ],
            'courses' => [
                [
                    'title' => 'Intro to ASL',
                    'slug' => 'intro-to-asl',
                    'description' => 'Imported course description',
                    'content' => '<p>Course body</p>',
                    'featured_image' => null,
                    'difficulty_level' => 'beginner',
                    'duration_minutes' => 90,
                    'price' => '0',
                    'currency_code' => 'USD',
                    'status' => 'published',
                    'instructor_email' => null,
                    'category_slug' => 'sign-language',
                    'lessons' => [
                        [
                            'title' => 'Lesson One',
                            'slug' => 'lesson-one',
                            'content' => '<p>Lesson body</p>',
                            'video_thumbnail' => null,
                            'sort_order' => 1,
                            'is_published' => true,
                            'assets' => [
                                'video_thumbnail' => [
                                    'type' => 'storage',
                                    'filename' => 'lesson-thumb.png',
                                    'mime_type' => 'image/png',
                                    'content_base64' => base64_encode('lesson-thumb'),
                                ],
                            ],
                        ],
                    ],
                    'assets' => [
                        'featured_image' => [
                            'type' => 'storage',
                            'filename' => 'course-cover.png',
                            'mime_type' => 'image/png',
                            'content_base64' => base64_encode('course-cover'),
                        ],
                    ],
                ],
            ],
        ];

        $upload = UploadedFile::fake()->createWithContent(
            'learning-transfer.json',
            json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        Livewire::actingAs($admin)
            ->test(ContentTransferManager::class)
            ->set('bundleTransferUpload', $upload)
            ->call('importBundleTransfer')
            ->assertHasNoErrors();

        $course = Course::where('slug', 'intro-to-asl')->firstOrFail();
        $lesson = Lesson::where('slug', 'lesson-one')->firstOrFail();

        $this->assertDatabaseHas('categories', ['slug' => 'sign-language', 'type' => 'course']);
        $this->assertSame($admin->id, $course->instructor_id);
        $this->assertSame($course->id, $lesson->course_id);
        Storage::disk('public')->assertExists($course->featured_image);
        Storage::disk('public')->assertExists($lesson->video_thumbnail);
        $this->assertDatabaseHas('content_transfer_logs', [
            'action' => 'import',
            'package_type' => ContentTransferService::BUNDLE_LEARNING,
        ]);
    }
}
