<?php

namespace App\Services;

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
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ContentTransferService
{
    public const PACKAGE_VERSION = 1;

    public const BUNDLE_BLOG = 'blog-content';

    public const BUNDLE_EVENTS = 'events-content';

    public const BUNDLE_GALLERY = 'gallery-content';

    public const BUNDLE_LEARNING = 'learning-content';

    public const BUNDLE_HERO_SLIDES = 'hero-slides-content';

    public const BUNDLE_EMAIL_TEMPLATES = 'email-templates-content';

    public const BUNDLE_SITE = 'site-content';

    public function exportPage(Page $page): array
    {
        return [
            'type' => 'page',
            'version' => self::PACKAGE_VERSION,
            'exported_at' => now()->toIso8601String(),
            'page' => $this->serializePage($page),
        ];
    }

    public function exportPages(?EloquentCollection $pages = null): array
    {
        $pages ??= Page::query()->orderBy('title')->get();

        return [
            'type' => 'pages',
            'version' => self::PACKAGE_VERSION,
            'exported_at' => now()->toIso8601String(),
            'pages' => $pages
                ->map(fn (Page $page): array => $this->serializePage($page))
                ->all(),
        ];
    }

    public function exportSiteSettings(): array
    {
        $settings = SiteSetting::query()
            ->orderBy('key')
            ->pluck('value', 'key')
            ->toArray();

        return [
            'type' => 'site-settings',
            'version' => self::PACKAGE_VERSION,
            'exported_at' => now()->toIso8601String(),
            'meta' => [
                'contains_sensitive_values' => $this->containsSensitiveSettings($settings),
            ],
            'settings' => $settings,
            'assets' => [
                'site_logo_path' => $this->serializeStorageAsset($settings['site_logo_path'] ?? null),
                'site_favicon_path' => $this->serializeStorageAsset($settings['site_favicon_path'] ?? null),
            ],
        ];
    }

    public function exportBundle(string $bundle): array
    {
        return match ($bundle) {
            self::BUNDLE_BLOG => $this->exportBlogContent(),
            self::BUNDLE_EVENTS => $this->exportEventsContent(),
            self::BUNDLE_GALLERY => $this->exportGalleryContent(),
            self::BUNDLE_LEARNING => $this->exportLearningContent(),
            self::BUNDLE_HERO_SLIDES => $this->exportHeroSlidesContent(),
            self::BUNDLE_EMAIL_TEMPLATES => $this->exportEmailTemplatesContent(),
            self::BUNDLE_SITE => $this->exportSiteContent(),
            default => throw new InvalidArgumentException('Unsupported content bundle ['.$bundle.'].'),
        };
    }

    public function importPagePackage(array $package): array
    {
        $this->assertPackageVersion($package);

        $type = (string) ($package['type'] ?? '');

        if ($type === 'page') {
            $pagePayload = $package['page'] ?? null;

            if (! is_array($pagePayload)) {
                throw new InvalidArgumentException('The page package is missing its page payload.');
            }

            return [$this->importSerializedPage($pagePayload)];
        }

        if ($type === 'pages') {
            $pagePayloads = $package['pages'] ?? null;

            if (! is_array($pagePayloads)) {
                throw new InvalidArgumentException('The pages package is missing its pages payload.');
            }

            return array_map(function ($pagePayload): string {
                if (! is_array($pagePayload)) {
                    throw new InvalidArgumentException('One of the pages in the package is invalid.');
                }

                return $this->importSerializedPage($pagePayload);
            }, $pagePayloads);
        }

        throw new InvalidArgumentException('This file is not a page transfer package.');
    }

    public function importSiteSettingsPackage(array $package): int
    {
        $this->assertPackageVersion($package);

        if (($package['type'] ?? null) !== 'site-settings') {
            throw new InvalidArgumentException('This file is not a site settings transfer package.');
        }

        $settings = $package['settings'] ?? null;

        if (! is_array($settings)) {
            throw new InvalidArgumentException('The site settings package is missing its settings payload.');
        }

        $currentLogoPath = SiteSetting::getValue('site_logo_path', '');
        $currentFaviconPath = SiteSetting::getValue('site_favicon_path', '');

        $settings['site_logo_path'] = $this->restoreAsset(
            is_array($package['assets']['site_logo_path'] ?? null) ? $package['assets']['site_logo_path'] : null,
            'settings/imported',
            $this->externalAssetPath($settings['site_logo_path'] ?? null)
        ) ?? '';

        $settings['site_favicon_path'] = $this->restoreAsset(
            is_array($package['assets']['site_favicon_path'] ?? null) ? $package['assets']['site_favicon_path'] : null,
            'settings/imported',
            $this->externalAssetPath($settings['site_favicon_path'] ?? null)
        ) ?? '';

        ksort($settings);

        foreach ($settings as $key => $value) {
            SiteSetting::setValue((string) $key, $this->normalizeSettingValue($value));
        }

        $this->deleteUnusedAsset($currentLogoPath, $settings['site_logo_path']);
        $this->deleteUnusedAsset($currentFaviconPath, $settings['site_favicon_path']);

        return count($settings);
    }

    public function importBundlePackage(array $package, ?User $actor = null): array
    {
        $summary = $this->summarizePackage($package);
        $type = $summary['type'];

        match ($type) {
            self::BUNDLE_BLOG => $this->importBlogPackage($package, $actor),
            self::BUNDLE_EVENTS => $this->importEventsPackage($package),
            self::BUNDLE_GALLERY => $this->importGalleryPackage($package),
            self::BUNDLE_LEARNING => $this->importLearningPackage($package, $actor),
            self::BUNDLE_HERO_SLIDES => $this->importHeroSlidesPackage($package),
            self::BUNDLE_EMAIL_TEMPLATES => $this->importEmailTemplatesPackage($package),
            self::BUNDLE_SITE => $this->importSiteContentPackage($package, $actor),
            default => throw new InvalidArgumentException('This file is not a supported content bundle.'),
        };

        return $summary;
    }

    public function summarizePackage(array $package): array
    {
        $this->assertPackageVersion($package);
        $type = (string) ($package['type'] ?? '');

        return match ($type) {
            'page' => [
                'type' => $type,
                'label' => 'Page',
                'item_count' => 1,
                'summary' => '1 page package',
                'details' => [
                    'slugs' => [(string) data_get($package, 'page.slug')],
                ],
            ],
            'pages' => [
                'type' => $type,
                'label' => 'Pages',
                'item_count' => count($package['pages'] ?? []),
                'summary' => count($package['pages'] ?? []).' pages package',
                'details' => [
                    'slugs' => array_values(array_map(
                        fn ($page): string => (string) ($page['slug'] ?? ''),
                        array_filter($package['pages'] ?? [], 'is_array')
                    )),
                ],
            ],
            'site-settings' => [
                'type' => $type,
                'label' => 'Site Settings',
                'item_count' => count($package['settings'] ?? []),
                'summary' => count($package['settings'] ?? []).' site settings values',
                'details' => [
                    'keys' => array_keys($package['settings'] ?? []),
                ],
            ],
            self::BUNDLE_BLOG => [
                'type' => $type,
                'label' => 'Blog Content',
                'item_count' => count($package['categories'] ?? []) + count($package['posts'] ?? []),
                'summary' => count($package['posts'] ?? []).' blog posts and '.count($package['categories'] ?? []).' blog categories',
                'details' => [
                    'post_slugs' => array_values(array_map(
                        fn ($post): string => (string) ($post['slug'] ?? ''),
                        array_filter($package['posts'] ?? [], 'is_array')
                    )),
                ],
            ],
            self::BUNDLE_EVENTS => [
                'type' => $type,
                'label' => 'Events Content',
                'item_count' => count($package['events'] ?? []),
                'summary' => count($package['events'] ?? []).' events',
                'details' => [
                    'slugs' => array_values(array_map(
                        fn ($event): string => (string) ($event['slug'] ?? ''),
                        array_filter($package['events'] ?? [], 'is_array')
                    )),
                ],
            ],
            self::BUNDLE_GALLERY => [
                'type' => $type,
                'label' => 'Gallery Content',
                'item_count' => count($package['items'] ?? []),
                'summary' => count($package['items'] ?? []).' gallery items',
                'details' => [
                    'slugs' => array_values(array_map(
                        fn ($item): string => (string) ($item['slug'] ?? ''),
                        array_filter($package['items'] ?? [], 'is_array')
                    )),
                ],
            ],
            self::BUNDLE_LEARNING => [
                'type' => $type,
                'label' => 'Learning Content',
                'item_count' => count($package['categories'] ?? []) + count($package['courses'] ?? []) + count($this->learningLessonPayloads($package)),
                'summary' => count($package['courses'] ?? []).' courses, '.count($this->learningLessonPayloads($package)).' lessons, and '.count($package['categories'] ?? []).' learning categories',
                'details' => [
                    'course_slugs' => array_values(array_map(
                        fn ($course): string => (string) ($course['slug'] ?? ''),
                        array_filter($package['courses'] ?? [], 'is_array')
                    )),
                ],
            ],
            self::BUNDLE_HERO_SLIDES => [
                'type' => $type,
                'label' => 'Hero Slides',
                'item_count' => count($package['slides'] ?? []),
                'summary' => count($package['slides'] ?? []).' hero slides',
                'details' => [],
            ],
            self::BUNDLE_EMAIL_TEMPLATES => [
                'type' => $type,
                'label' => 'Email Templates',
                'item_count' => count($package['templates'] ?? []),
                'summary' => count($package['templates'] ?? []).' email templates',
                'details' => [
                    'keys' => array_values(array_map(
                        fn ($template): string => (string) ($template['key'] ?? ''),
                        array_filter($package['templates'] ?? [], 'is_array')
                    )),
                ],
            ],
            self::BUNDLE_SITE => $this->summarizeSiteContentPackage($package),
            default => throw new InvalidArgumentException('Unsupported transfer package type ['.$type.'].'),
        };
    }

    protected function exportBlogContent(): array
    {
        $posts = BlogPost::query()
            ->with(['author', 'category'])
            ->orderBy('published_at')
            ->orderBy('title')
            ->get();

        $categories = Category::query()
            ->whereIn('id', $posts->pluck('category_id')->filter()->unique())
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return [
            'type' => self::BUNDLE_BLOG,
            'version' => self::PACKAGE_VERSION,
            'exported_at' => now()->toIso8601String(),
            'categories' => $categories->map(fn (Category $category): array => $this->serializeCategory($category))->all(),
            'posts' => $posts->map(fn (BlogPost $post): array => $this->serializeBlogPost($post))->all(),
        ];
    }

    protected function exportEventsContent(): array
    {
        return [
            'type' => self::BUNDLE_EVENTS,
            'version' => self::PACKAGE_VERSION,
            'exported_at' => now()->toIso8601String(),
            'events' => Event::query()
                ->orderBy('start_date')
                ->orderBy('title')
                ->get()
                ->map(fn (Event $event): array => $this->serializeEvent($event))
                ->all(),
        ];
    }

    protected function exportGalleryContent(): array
    {
        return [
            'type' => self::BUNDLE_GALLERY,
            'version' => self::PACKAGE_VERSION,
            'exported_at' => now()->toIso8601String(),
            'items' => GalleryItem::query()
                ->orderBy('sort_order')
                ->orderBy('title')
                ->get()
                ->map(fn (GalleryItem $item): array => $this->serializeGalleryItem($item))
                ->all(),
        ];
    }

    protected function exportLearningContent(): array
    {
        $courses = Course::query()
            ->with(['instructor', 'category', 'lessons'])
            ->orderBy('title')
            ->get();

        $categories = Category::query()
            ->whereIn('id', $courses->pluck('category_id')->filter()->unique())
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return [
            'type' => self::BUNDLE_LEARNING,
            'version' => self::PACKAGE_VERSION,
            'exported_at' => now()->toIso8601String(),
            'categories' => $categories->map(fn (Category $category): array => $this->serializeCategory($category))->all(),
            'courses' => $courses->map(fn (Course $course): array => $this->serializeCourse($course))->all(),
        ];
    }

    protected function exportHeroSlidesContent(): array
    {
        return [
            'type' => self::BUNDLE_HERO_SLIDES,
            'version' => self::PACKAGE_VERSION,
            'exported_at' => now()->toIso8601String(),
            'slides' => HeroSlide::query()
                ->ordered()
                ->get()
                ->map(fn (HeroSlide $slide): array => $this->serializeHeroSlide($slide))
                ->all(),
        ];
    }

    protected function exportEmailTemplatesContent(): array
    {
        return [
            'type' => self::BUNDLE_EMAIL_TEMPLATES,
            'version' => self::PACKAGE_VERSION,
            'exported_at' => now()->toIso8601String(),
            'templates' => EmailTemplate::query()
                ->orderBy('key')
                ->get()
                ->map(fn (EmailTemplate $template): array => $this->serializeEmailTemplate($template))
                ->all(),
        ];
    }

    protected function exportSiteContent(): array
    {
        return [
            'type' => self::BUNDLE_SITE,
            'version' => self::PACKAGE_VERSION,
            'exported_at' => now()->toIso8601String(),
            'packages' => [
                'pages' => $this->exportPages(),
                'site_settings' => $this->exportSiteSettings(),
                'blog' => $this->exportBlogContent(),
                'events' => $this->exportEventsContent(),
                'gallery' => $this->exportGalleryContent(),
                'learning' => $this->exportLearningContent(),
                'hero_slides' => $this->exportHeroSlidesContent(),
                'email_templates' => $this->exportEmailTemplatesContent(),
            ],
        ];
    }

    protected function importBlogPackage(array $package, ?User $actor): void
    {
        $this->assertPackageVersion($package);

        if (($package['type'] ?? null) !== self::BUNDLE_BLOG) {
            throw new InvalidArgumentException('This file is not a blog content package.');
        }

        $categoryMap = $this->importCategories($package['categories'] ?? []);

        foreach ($package['posts'] ?? [] as $postPayload) {
            if (! is_array($postPayload)) {
                throw new InvalidArgumentException('One of the blog posts in the package is invalid.');
            }

            $this->importSerializedBlogPost($postPayload, $categoryMap, $actor);
        }
    }

    protected function importEventsPackage(array $package): void
    {
        $this->assertPackageVersion($package);

        if (($package['type'] ?? null) !== self::BUNDLE_EVENTS) {
            throw new InvalidArgumentException('This file is not an events content package.');
        }

        foreach ($package['events'] ?? [] as $eventPayload) {
            if (! is_array($eventPayload)) {
                throw new InvalidArgumentException('One of the events in the package is invalid.');
            }

            $this->importSerializedEvent($eventPayload);
        }
    }

    protected function importGalleryPackage(array $package): void
    {
        $this->assertPackageVersion($package);

        if (($package['type'] ?? null) !== self::BUNDLE_GALLERY) {
            throw new InvalidArgumentException('This file is not a gallery content package.');
        }

        foreach ($package['items'] ?? [] as $itemPayload) {
            if (! is_array($itemPayload)) {
                throw new InvalidArgumentException('One of the gallery items in the package is invalid.');
            }

            $this->importSerializedGalleryItem($itemPayload);
        }
    }

    protected function importLearningPackage(array $package, ?User $actor): void
    {
        $this->assertPackageVersion($package);

        if (($package['type'] ?? null) !== self::BUNDLE_LEARNING) {
            throw new InvalidArgumentException('This file is not a learning content package.');
        }

        $categoryMap = $this->importCategories($package['categories'] ?? []);

        foreach ($package['courses'] ?? [] as $coursePayload) {
            if (! is_array($coursePayload)) {
                throw new InvalidArgumentException('One of the courses in the package is invalid.');
            }

            $this->importSerializedCourse($coursePayload, $categoryMap, $actor);
        }
    }

    protected function importHeroSlidesPackage(array $package): void
    {
        $this->assertPackageVersion($package);

        if (($package['type'] ?? null) !== self::BUNDLE_HERO_SLIDES) {
            throw new InvalidArgumentException('This file is not a hero slides package.');
        }

        foreach ($package['slides'] ?? [] as $slidePayload) {
            if (! is_array($slidePayload)) {
                throw new InvalidArgumentException('One of the hero slides in the package is invalid.');
            }

            $this->importSerializedHeroSlide($slidePayload);
        }
    }

    protected function importEmailTemplatesPackage(array $package): void
    {
        $this->assertPackageVersion($package);

        if (($package['type'] ?? null) !== self::BUNDLE_EMAIL_TEMPLATES) {
            throw new InvalidArgumentException('This file is not an email templates package.');
        }

        foreach ($package['templates'] ?? [] as $templatePayload) {
            if (! is_array($templatePayload)) {
                throw new InvalidArgumentException('One of the email templates in the package is invalid.');
            }

            $this->importSerializedEmailTemplate($templatePayload);
        }
    }

    protected function importSiteContentPackage(array $package, ?User $actor): void
    {
        $this->assertPackageVersion($package);

        if (($package['type'] ?? null) !== self::BUNDLE_SITE) {
            throw new InvalidArgumentException('This file is not a full site content package.');
        }

        $packages = $package['packages'] ?? null;

        if (! is_array($packages)) {
            throw new InvalidArgumentException('The full site content package is missing its nested packages.');
        }

        if (isset($packages['pages']) && is_array($packages['pages'])) {
            $this->importPagePackage($packages['pages']);
        }

        if (isset($packages['site_settings']) && is_array($packages['site_settings'])) {
            $this->importSiteSettingsPackage($packages['site_settings']);
        }

        if (isset($packages['blog']) && is_array($packages['blog'])) {
            $this->importBlogPackage($packages['blog'], $actor);
        }

        if (isset($packages['events']) && is_array($packages['events'])) {
            $this->importEventsPackage($packages['events']);
        }

        if (isset($packages['gallery']) && is_array($packages['gallery'])) {
            $this->importGalleryPackage($packages['gallery']);
        }

        if (isset($packages['learning']) && is_array($packages['learning'])) {
            $this->importLearningPackage($packages['learning'], $actor);
        }

        if (isset($packages['hero_slides']) && is_array($packages['hero_slides'])) {
            $this->importHeroSlidesPackage($packages['hero_slides']);
        }

        if (isset($packages['email_templates']) && is_array($packages['email_templates'])) {
            $this->importEmailTemplatesPackage($packages['email_templates']);
        }
    }

    protected function serializePage(Page $page): array
    {
        return [
            'slug' => $page->slug,
            'title' => $page->title,
            'content' => $page->content,
            'excerpt' => $page->getRawOriginal('excerpt'),
            'meta_title' => $page->getRawOriginal('meta_title'),
            'meta_description' => $page->getRawOriginal('meta_description'),
            'meta_image' => $page->meta_image,
            'status' => $page->status,
            'show_media_sidebar' => (bool) $page->show_media_sidebar,
            'order' => (int) $page->order,
            'metadata' => $page->metadata,
            'navigation' => $page->navigation,
            'sections' => $page->sections,
            'assets' => [
                'meta_image' => $this->serializeStorageAsset($page->meta_image),
                'partner_logos' => $this->serializePartnerLogoAssets($page->sections),
            ],
        ];
    }

    protected function serializeCategory(Category $category): array
    {
        return [
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'type' => $category->type,
            'is_active' => (bool) $category->is_active,
            'sort_order' => (int) $category->sort_order,
        ];
    }

    protected function serializeBlogPost(BlogPost $post): array
    {
        return [
            'title' => $post->title,
            'slug' => $post->slug,
            'excerpt' => $post->getRawOriginal('excerpt'),
            'content' => $post->content,
            'featured_image' => $post->featured_image,
            'youtube_video_id' => $post->youtube_video_id,
            'video_thumbnail' => $post->video_thumbnail,
            'status' => $post->status,
            'published_at' => $post->published_at?->toIso8601String(),
            'author_email' => $post->author?->email,
            'author_name' => $post->author?->name,
            'category_slug' => $post->category?->slug,
            'tags' => $post->tags,
            'views' => (int) $post->views,
            'is_featured' => (bool) $post->is_featured,
            'allow_comments' => (bool) $post->allow_comments,
            'assets' => [
                'featured_image' => $this->serializeStorageAsset($post->featured_image),
                'video_thumbnail' => $this->serializeStorageAsset($post->video_thumbnail),
            ],
        ];
    }

    protected function serializeEvent(Event $event): array
    {
        return [
            'title' => $event->title,
            'slug' => $event->slug,
            'description' => $event->description,
            'excerpt' => $event->getRawOriginal('excerpt'),
            'start_date' => $event->start_date?->toIso8601String(),
            'end_date' => $event->end_date?->toIso8601String(),
            'time' => $event->time,
            'location' => $event->location,
            'venue' => $event->venue,
            'price' => $event->price,
            'registration_required' => (bool) $event->registration_required,
            'registration_url' => $event->registration_url,
            'image' => $event->image,
            'status' => $event->status,
            'is_virtual' => (bool) $event->is_virtual,
            'meeting_link' => $event->meeting_link,
            'max_attendees' => $event->max_attendees,
            'metadata' => $event->metadata,
            'assets' => [
                'image' => $this->serializeStorageAsset($event->image),
            ],
        ];
    }

    protected function serializeGalleryItem(GalleryItem $item): array
    {
        return [
            'title' => $item->title,
            'slug' => $item->slug,
            'description' => $item->description,
            'type' => $item->type,
            'event_name' => $item->event_name,
            'captured_at' => $item->captured_at?->format('Y-m-d'),
            'is_featured' => (bool) $item->is_featured,
            'sort_order' => (int) $item->sort_order,
            'status' => $item->status,
            'image_paths' => $item->normalized_image_paths,
            'assets' => [
                'images' => collect($item->normalized_image_paths)
                    ->values()
                    ->map(fn (string $path, int $index): array => [
                        'index' => $index,
                        'asset' => $this->serializeStorageAsset($path),
                    ])
                    ->all(),
            ],
        ];
    }

    protected function serializeCourse(Course $course): array
    {
        return [
            'title' => $course->title,
            'slug' => $course->slug,
            'description' => $course->description,
            'content' => $course->content,
            'featured_image' => $course->featured_image,
            'intro_video_url' => $course->intro_video_url,
            'difficulty_level' => $course->difficulty_level,
            'duration_minutes' => (int) $course->duration_minutes,
            'price' => (string) $course->price,
            'currency_code' => $course->currency_code,
            'status' => $course->status,
            'instructor_email' => $course->instructor?->email,
            'instructor_name' => $course->instructor?->name,
            'category_slug' => $course->category?->slug,
            'max_students' => $course->max_students,
            'start_date' => $course->start_date?->toIso8601String(),
            'end_date' => $course->end_date?->toIso8601String(),
            'prerequisites' => $course->prerequisites,
            'learning_outcomes' => $course->learning_outcomes,
            'is_certificate_enabled' => (bool) $course->is_certificate_enabled,
            'is_featured' => (bool) $course->is_featured,
            'enrollment_count' => (int) $course->enrollment_count,
            'rating' => (string) $course->rating,
            'review_count' => (int) $course->review_count,
            'assets' => [
                'featured_image' => $this->serializeStorageAsset($course->featured_image),
            ],
            'lessons' => $course->lessons
                ->sortBy('sort_order')
                ->values()
                ->map(fn (Lesson $lesson): array => $this->serializeLesson($lesson))
                ->all(),
        ];
    }

    protected function serializeLesson(Lesson $lesson): array
    {
        return [
            'title' => $lesson->title,
            'slug' => $lesson->slug,
            'description' => $lesson->description,
            'content' => $lesson->content,
            'video_url' => $lesson->video_url,
            'video_thumbnail' => $lesson->video_thumbnail,
            'type' => $lesson->type,
            'duration_minutes' => (int) $lesson->duration_minutes,
            'sort_order' => (int) $lesson->sort_order,
            'is_free' => (bool) $lesson->is_free,
            'is_published' => (bool) $lesson->is_published,
            'assets' => [
                'video_thumbnail' => $this->serializeStorageAsset($lesson->video_thumbnail),
            ],
        ];
    }

    protected function serializeHeroSlide(HeroSlide $slide): array
    {
        return [
            'title' => $slide->title,
            'subtitle' => $slide->subtitle,
            'content' => $slide->content,
            'cta_label' => $slide->cta_label,
            'cta_url' => $slide->cta_url,
            'image_path' => $slide->image_path,
            'sort_order' => (int) $slide->sort_order,
            'is_active' => (bool) $slide->is_active,
            'assets' => [
                'image_path' => $this->serializeStorageAsset($slide->image_path),
            ],
        ];
    }

    protected function serializeEmailTemplate(EmailTemplate $template): array
    {
        return [
            'key' => $template->key,
            'name' => $template->name,
            'description' => $template->description,
            'subject' => $template->subject,
            'body' => $template->body,
            'is_active' => (bool) $template->is_active,
        ];
    }

    protected function importSerializedPage(array $pagePayload): string
    {
        $slug = Str::slug((string) ($pagePayload['slug'] ?? ''));
        $content = (string) ($pagePayload['content'] ?? '');

        if ($slug === '') {
            throw new InvalidArgumentException('Each page package must include a valid slug.');
        }

        if (trim($content) === '') {
            throw new InvalidArgumentException("The page package for [{$slug}] is missing its content.");
        }

        $page = Page::query()->firstOrNew(['slug' => $slug]);
        $oldMetaImagePath = $page->meta_image;
        $oldPartnerLogoPaths = $this->extractPartnerLogoPaths($page->sections);

        $sections = is_array($pagePayload['sections'] ?? null) ? $pagePayload['sections'] : null;
        $sections = $this->restorePartnerLogoAssets(
            $sections,
            is_array($pagePayload['assets']['partner_logos'] ?? null) ? $pagePayload['assets']['partner_logos'] : []
        );

        $metaImagePath = $this->restoreAsset(
            is_array($pagePayload['assets']['meta_image'] ?? null) ? $pagePayload['assets']['meta_image'] : null,
            'pages/imported',
            $this->externalAssetPath($pagePayload['meta_image'] ?? null)
        );

        $status = (string) ($pagePayload['status'] ?? 'draft');
        if (! in_array($status, ['draft', 'published', 'archived'], true)) {
            $status = 'draft';
        }

        $page->fill([
            'title' => (string) ($pagePayload['title'] ?? Str::headline($slug)),
            'slug' => $slug,
            'content' => $content,
            'excerpt' => $this->nullableString($pagePayload['excerpt'] ?? null),
            'meta_title' => $this->nullableString($pagePayload['meta_title'] ?? null),
            'meta_description' => $this->nullableString($pagePayload['meta_description'] ?? null),
            'meta_image' => $metaImagePath,
            'status' => $status,
            'show_media_sidebar' => (bool) ($pagePayload['show_media_sidebar'] ?? false),
            'order' => (int) ($pagePayload['order'] ?? 0),
            'metadata' => is_array($pagePayload['metadata'] ?? null) ? $pagePayload['metadata'] : null,
            'navigation' => is_array($pagePayload['navigation'] ?? null) ? $pagePayload['navigation'] : null,
            'sections' => $sections,
        ]);
        $page->save();

        $this->deleteUnusedAsset($oldMetaImagePath, $page->meta_image);
        $this->deleteUnusedAssets($oldPartnerLogoPaths, $this->extractPartnerLogoPaths($page->sections));

        return $page->slug;
    }

    protected function importCategories(array $categoryPayloads): array
    {
        $map = [];

        foreach ($categoryPayloads as $categoryPayload) {
            if (! is_array($categoryPayload)) {
                throw new InvalidArgumentException('One of the categories in the package is invalid.');
            }

            $slug = Str::slug((string) ($categoryPayload['slug'] ?? ''));
            if ($slug === '') {
                throw new InvalidArgumentException('A category package entry is missing a valid slug.');
            }

            $category = Category::query()->firstOrNew(['slug' => $slug]);
            $category->fill([
                'name' => (string) ($categoryPayload['name'] ?? Str::headline($slug)),
                'slug' => $slug,
                'description' => $this->nullableString($categoryPayload['description'] ?? null),
                'type' => (string) ($categoryPayload['type'] ?? 'blog'),
                'is_active' => (bool) ($categoryPayload['is_active'] ?? true),
                'sort_order' => (int) ($categoryPayload['sort_order'] ?? 0),
            ]);
            $category->save();

            $map[$slug] = $category;
        }

        return $map;
    }

    protected function importSerializedBlogPost(array $postPayload, array $categoryMap, ?User $actor): string
    {
        $slug = Str::slug((string) ($postPayload['slug'] ?? ''));
        $content = (string) ($postPayload['content'] ?? '');

        if ($slug === '') {
            throw new InvalidArgumentException('A blog post package entry is missing a valid slug.');
        }

        if (trim($content) === '') {
            throw new InvalidArgumentException("The blog post package for [{$slug}] is missing its content.");
        }

        $post = BlogPost::query()->firstOrNew(['slug' => $slug]);
        $oldFeaturedImage = $post->featured_image;
        $oldVideoThumbnail = $post->video_thumbnail;

        $categorySlug = $this->nullableString($postPayload['category_slug'] ?? null);
        $category = $categorySlug !== null ? ($categoryMap[$categorySlug] ?? Category::query()->where('slug', $categorySlug)->first()) : null;
        $author = $this->resolveUser($this->nullableString($postPayload['author_email'] ?? null), $actor, 'blog post');

        $featuredImage = $this->restoreAsset(
            is_array($postPayload['assets']['featured_image'] ?? null) ? $postPayload['assets']['featured_image'] : null,
            'blog/imported',
            $this->externalAssetPath($postPayload['featured_image'] ?? null)
        );

        $videoThumbnail = $this->restoreAsset(
            is_array($postPayload['assets']['video_thumbnail'] ?? null) ? $postPayload['assets']['video_thumbnail'] : null,
            'blog/imported',
            $this->externalAssetPath($postPayload['video_thumbnail'] ?? null)
        );

        $post->fill([
            'title' => (string) ($postPayload['title'] ?? Str::headline($slug)),
            'slug' => $slug,
            'excerpt' => $this->nullableString($postPayload['excerpt'] ?? null),
            'content' => $content,
            'featured_image' => $featuredImage,
            'youtube_video_id' => $this->nullableString($postPayload['youtube_video_id'] ?? null),
            'video_thumbnail' => $videoThumbnail,
            'status' => (string) ($postPayload['status'] ?? 'draft'),
            'published_at' => $this->nullableString($postPayload['published_at'] ?? null),
            'author_id' => $author->id,
            'category_id' => $category?->id,
            'tags' => is_array($postPayload['tags'] ?? null) ? $postPayload['tags'] : [],
            'views' => (int) ($postPayload['views'] ?? 0),
            'is_featured' => (bool) ($postPayload['is_featured'] ?? false),
            'allow_comments' => (bool) ($postPayload['allow_comments'] ?? true),
        ]);
        $post->save();

        $this->deleteUnusedAsset($oldFeaturedImage, $post->featured_image);
        $this->deleteUnusedAsset($oldVideoThumbnail, $post->video_thumbnail);

        return $post->slug;
    }

    protected function importSerializedEvent(array $eventPayload): string
    {
        $slug = Str::slug((string) ($eventPayload['slug'] ?? ''));

        if ($slug === '') {
            throw new InvalidArgumentException('An event package entry is missing a valid slug.');
        }

        $event = Event::query()->firstOrNew(['slug' => $slug]);
        $oldImage = $event->image;

        $image = $this->restoreAsset(
            is_array($eventPayload['assets']['image'] ?? null) ? $eventPayload['assets']['image'] : null,
            'events/imported',
            $this->externalAssetPath($eventPayload['image'] ?? null)
        );

        $event->fill([
            'title' => (string) ($eventPayload['title'] ?? Str::headline($slug)),
            'slug' => $slug,
            'description' => (string) ($eventPayload['description'] ?? ''),
            'excerpt' => $this->nullableString($eventPayload['excerpt'] ?? null),
            'start_date' => $this->nullableString($eventPayload['start_date'] ?? null),
            'end_date' => $this->nullableString($eventPayload['end_date'] ?? null),
            'time' => $this->nullableString($eventPayload['time'] ?? null),
            'location' => $this->nullableString($eventPayload['location'] ?? null),
            'venue' => $this->nullableString($eventPayload['venue'] ?? null),
            'price' => $this->nullableString($eventPayload['price'] ?? null),
            'registration_required' => (bool) ($eventPayload['registration_required'] ?? false),
            'registration_url' => $this->nullableString($eventPayload['registration_url'] ?? null),
            'image' => $image,
            'status' => (string) ($eventPayload['status'] ?? 'upcoming'),
            'is_virtual' => (bool) ($eventPayload['is_virtual'] ?? false),
            'meeting_link' => $this->nullableString($eventPayload['meeting_link'] ?? null),
            'max_attendees' => $eventPayload['max_attendees'] ?? null,
            'metadata' => is_array($eventPayload['metadata'] ?? null) ? $eventPayload['metadata'] : null,
        ]);
        $event->save();

        $this->deleteUnusedAsset($oldImage, $event->image);

        return $event->slug;
    }

    protected function importSerializedGalleryItem(array $itemPayload): string
    {
        $slug = Str::slug((string) ($itemPayload['slug'] ?? ''));

        if ($slug === '') {
            throw new InvalidArgumentException('A gallery item package entry is missing a valid slug.');
        }

        $item = GalleryItem::query()->firstOrNew(['slug' => $slug]);
        $oldImagePaths = $item->normalized_image_paths;

        $restoredImagePaths = [];
        $assetsByIndex = collect($itemPayload['assets']['images'] ?? [])
            ->filter(fn ($entry): bool => is_array($entry) && array_key_exists('index', $entry))
            ->keyBy('index');

        foreach ((array) ($itemPayload['image_paths'] ?? []) as $index => $path) {
            $assetEntry = $assetsByIndex->get($index);
            $assetPayload = is_array($assetEntry) ? ($assetEntry['asset'] ?? null) : null;
            $restoredPath = $this->restoreAsset(
                is_array($assetPayload) ? $assetPayload : null,
                'gallery/imported',
                $this->externalAssetPath($path)
            );

            if ($restoredPath !== null) {
                $restoredImagePaths[] = $restoredPath;
            }
        }

        if ($restoredImagePaths === []) {
            throw new InvalidArgumentException("The gallery item package for [{$slug}] does not contain any restorable images.");
        }

        $item->fill([
            'title' => (string) ($itemPayload['title'] ?? Str::headline($slug)),
            'slug' => $slug,
            'description' => $this->nullableString($itemPayload['description'] ?? null),
            'image_path' => $restoredImagePaths[0],
            'image_paths' => $restoredImagePaths,
            'type' => (string) ($itemPayload['type'] ?? 'activity'),
            'event_name' => $this->nullableString($itemPayload['event_name'] ?? null),
            'captured_at' => $this->nullableString($itemPayload['captured_at'] ?? null),
            'is_featured' => (bool) ($itemPayload['is_featured'] ?? false),
            'sort_order' => (int) ($itemPayload['sort_order'] ?? 0),
            'status' => (string) ($itemPayload['status'] ?? 'published'),
        ]);
        $item->save();

        $this->deleteUnusedAssets($oldImagePaths, $restoredImagePaths);

        return $item->slug;
    }

    protected function importSerializedCourse(array $coursePayload, array $categoryMap, ?User $actor): string
    {
        $slug = Str::slug((string) ($coursePayload['slug'] ?? ''));

        if ($slug === '') {
            throw new InvalidArgumentException('A course package entry is missing a valid slug.');
        }

        $course = Course::query()->firstOrNew(['slug' => $slug]);
        $oldFeaturedImage = $course->featured_image;

        $categorySlug = $this->nullableString($coursePayload['category_slug'] ?? null);
        $category = $categorySlug !== null ? ($categoryMap[$categorySlug] ?? Category::query()->where('slug', $categorySlug)->first()) : null;
        $instructor = $this->resolveUser($this->nullableString($coursePayload['instructor_email'] ?? null), $actor, 'course');

        $featuredImage = $this->restoreAsset(
            is_array($coursePayload['assets']['featured_image'] ?? null) ? $coursePayload['assets']['featured_image'] : null,
            'courses/imported',
            $this->externalAssetPath($coursePayload['featured_image'] ?? null)
        );

        $course->fill([
            'title' => (string) ($coursePayload['title'] ?? Str::headline($slug)),
            'slug' => $slug,
            'description' => (string) ($coursePayload['description'] ?? ''),
            'content' => $this->nullableString($coursePayload['content'] ?? null),
            'featured_image' => $featuredImage,
            'intro_video_url' => $this->nullableString($coursePayload['intro_video_url'] ?? null),
            'difficulty_level' => (string) ($coursePayload['difficulty_level'] ?? 'beginner'),
            'duration_minutes' => (int) ($coursePayload['duration_minutes'] ?? 0),
            'price' => (float) ($coursePayload['price'] ?? 0),
            'currency_code' => (string) ($coursePayload['currency_code'] ?? 'USD'),
            'status' => (string) ($coursePayload['status'] ?? 'draft'),
            'instructor_id' => $instructor->id,
            'category_id' => $category?->id,
            'max_students' => $coursePayload['max_students'] ?? null,
            'start_date' => $this->nullableString($coursePayload['start_date'] ?? null),
            'end_date' => $this->nullableString($coursePayload['end_date'] ?? null),
            'prerequisites' => is_array($coursePayload['prerequisites'] ?? null) ? $coursePayload['prerequisites'] : null,
            'learning_outcomes' => is_array($coursePayload['learning_outcomes'] ?? null) ? $coursePayload['learning_outcomes'] : null,
            'is_certificate_enabled' => (bool) ($coursePayload['is_certificate_enabled'] ?? true),
            'is_featured' => (bool) ($coursePayload['is_featured'] ?? false),
            'enrollment_count' => (int) ($coursePayload['enrollment_count'] ?? 0),
            'rating' => (float) ($coursePayload['rating'] ?? 0),
            'review_count' => (int) ($coursePayload['review_count'] ?? 0),
        ]);
        $course->save();

        $this->deleteUnusedAsset($oldFeaturedImage, $course->featured_image);

        foreach ((array) ($coursePayload['lessons'] ?? []) as $lessonPayload) {
            if (! is_array($lessonPayload)) {
                throw new InvalidArgumentException('One of the lessons in the course package is invalid.');
            }

            $this->importSerializedLesson($lessonPayload, $course);
        }

        return $course->slug;
    }

    protected function importSerializedLesson(array $lessonPayload, Course $course): string
    {
        $slug = Str::slug((string) ($lessonPayload['slug'] ?? ''));
        $content = (string) ($lessonPayload['content'] ?? '');

        if ($slug === '') {
            throw new InvalidArgumentException('A lesson package entry is missing a valid slug.');
        }

        if (trim($content) === '') {
            throw new InvalidArgumentException("The lesson package for [{$slug}] is missing its content.");
        }

        $lesson = Lesson::query()->firstOrNew(['slug' => $slug]);
        $oldVideoThumbnail = $lesson->video_thumbnail;

        $videoThumbnail = $this->restoreAsset(
            is_array($lessonPayload['assets']['video_thumbnail'] ?? null) ? $lessonPayload['assets']['video_thumbnail'] : null,
            'lessons/imported',
            $this->externalAssetPath($lessonPayload['video_thumbnail'] ?? null)
        );

        $lesson->fill([
            'title' => (string) ($lessonPayload['title'] ?? Str::headline($slug)),
            'slug' => $slug,
            'description' => $this->nullableString($lessonPayload['description'] ?? null),
            'content' => $content,
            'video_url' => $this->nullableString($lessonPayload['video_url'] ?? null),
            'video_thumbnail' => $videoThumbnail,
            'type' => (string) ($lessonPayload['type'] ?? 'video'),
            'duration_minutes' => (int) ($lessonPayload['duration_minutes'] ?? 0),
            'sort_order' => (int) ($lessonPayload['sort_order'] ?? 0),
            'is_free' => (bool) ($lessonPayload['is_free'] ?? false),
            'is_published' => (bool) ($lessonPayload['is_published'] ?? false),
            'course_id' => $course->id,
        ]);
        $lesson->save();

        $this->deleteUnusedAsset($oldVideoThumbnail, $lesson->video_thumbnail);

        return $lesson->slug;
    }

    protected function importSerializedHeroSlide(array $slidePayload): string
    {
        $title = $this->nullableString($slidePayload['title'] ?? null);

        if ($title === null) {
            throw new InvalidArgumentException('A hero slide package entry is missing its title.');
        }

        $slide = HeroSlide::query()->firstOrNew(['title' => $title]);
        $oldImagePath = $slide->image_path;

        $imagePath = $this->restoreAsset(
            is_array($slidePayload['assets']['image_path'] ?? null) ? $slidePayload['assets']['image_path'] : null,
            'hero-slides/imported',
            $this->externalAssetPath($slidePayload['image_path'] ?? null)
        );

        $slide->fill([
            'title' => $title,
            'subtitle' => $this->nullableString($slidePayload['subtitle'] ?? null),
            'content' => $this->nullableString($slidePayload['content'] ?? null),
            'cta_label' => $this->nullableString($slidePayload['cta_label'] ?? null),
            'cta_url' => $this->nullableString($slidePayload['cta_url'] ?? null),
            'image_path' => $imagePath,
            'sort_order' => (int) ($slidePayload['sort_order'] ?? 0),
            'is_active' => (bool) ($slidePayload['is_active'] ?? true),
        ]);
        $slide->save();

        $this->deleteUnusedAsset($oldImagePath, $slide->image_path);

        return $slide->title;
    }

    protected function importSerializedEmailTemplate(array $templatePayload): string
    {
        $key = $this->nullableString($templatePayload['key'] ?? null);

        if ($key === null) {
            throw new InvalidArgumentException('An email template package entry is missing its key.');
        }

        $template = EmailTemplate::query()->firstOrNew(['key' => $key]);
        $template->fill([
            'key' => $key,
            'name' => (string) ($templatePayload['name'] ?? Str::headline(str_replace('_', ' ', $key))),
            'description' => $this->nullableString($templatePayload['description'] ?? null),
            'subject' => (string) ($templatePayload['subject'] ?? ''),
            'body' => (string) ($templatePayload['body'] ?? ''),
            'is_active' => (bool) ($templatePayload['is_active'] ?? true),
        ]);
        $template->save();

        return $template->key;
    }

    protected function serializeStorageAsset(?string $path): ?array
    {
        $path = $this->nullableString($path);

        if ($path === null) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return [
                'type' => 'external',
                'url' => $path,
            ];
        }

        if (! Storage::disk('public')->exists($path)) {
            return [
                'type' => 'missing',
                'path' => $path,
            ];
        }

        return [
            'type' => 'storage',
            'path' => $path,
            'filename' => basename($path),
            'mime_type' => Storage::disk('public')->mimeType($path) ?: 'application/octet-stream',
            'content_base64' => base64_encode(Storage::disk('public')->get($path)),
        ];
    }

    protected function serializePartnerLogoAssets(?array $sections): array
    {
        $partners = data_get($sections, 'trust.partners', []);

        if (! is_array($partners)) {
            return [];
        }

        $assets = [];

        foreach ($partners as $index => $partner) {
            $asset = $this->serializeStorageAsset($partner['logo_path'] ?? null);

            if ($asset === null) {
                continue;
            }

            $assets[] = [
                'index' => (int) $index,
                'asset' => $asset,
            ];
        }

        return $assets;
    }

    protected function restorePartnerLogoAssets(?array $sections, array $assets): ?array
    {
        if (! is_array($sections)) {
            return $sections;
        }

        $partners = data_get($sections, 'trust.partners');

        if (! is_array($partners)) {
            return $sections;
        }

        $assetsByIndex = collect($assets)
            ->filter(fn ($entry): bool => is_array($entry) && array_key_exists('index', $entry))
            ->keyBy('index');

        foreach ($partners as $index => $partner) {
            $assetEntry = $assetsByIndex->get($index);
            $assetPayload = is_array($assetEntry) ? ($assetEntry['asset'] ?? null) : null;
            $fallbackPath = $this->externalAssetPath($partner['logo_path'] ?? null);

            data_set(
                $sections,
                "trust.partners.{$index}.logo_path",
                $this->restoreAsset(
                    is_array($assetPayload) ? $assetPayload : null,
                    'pages/partners/imported',
                    $fallbackPath
                )
            );
        }

        return $sections;
    }

    protected function restoreAsset(?array $assetPayload, string $directory, ?string $fallback = null): ?string
    {
        if ($assetPayload === null) {
            return $fallback;
        }

        $type = (string) ($assetPayload['type'] ?? 'storage');

        if ($type === 'external') {
            return $this->externalAssetPath($assetPayload['url'] ?? null) ?? $fallback;
        }

        if ($type !== 'storage') {
            return $fallback;
        }

        $encoded = $assetPayload['content_base64'] ?? null;
        if (! is_string($encoded) || $encoded === '') {
            throw new InvalidArgumentException('One of the imported assets is missing its file contents.');
        }

        $decoded = base64_decode($encoded, true);
        if ($decoded === false) {
            throw new InvalidArgumentException('One of the imported assets could not be decoded.');
        }

        $filename = (string) ($assetPayload['filename'] ?? 'asset');
        $extension = strtolower((string) pathinfo($filename, PATHINFO_EXTENSION));

        if ($extension === '') {
            $extension = $this->extensionFromMime((string) ($assetPayload['mime_type'] ?? ''));
        }

        $path = trim($directory, '/').'/'.Str::uuid()->toString();
        if ($extension !== '') {
            $path .= '.'.$extension;
        }

        Storage::disk('public')->put($path, $decoded);

        return $path;
    }

    protected function extensionFromMime(string $mimeType): string
    {
        return match (strtolower($mimeType)) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'image/gif' => 'gif',
            'image/x-icon', 'image/vnd.microsoft.icon' => 'ico',
            default => '',
        };
    }

    protected function extractPartnerLogoPaths(?array $sections): array
    {
        $partners = data_get($sections, 'trust.partners', []);

        if (! is_array($partners)) {
            return [];
        }

        return array_values(array_filter(array_map(function ($partner): ?string {
            $path = $this->nullableString($partner['logo_path'] ?? null);

            if ($path === null || Str::startsWith($path, ['http://', 'https://'])) {
                return null;
            }

            return $path;
        }, $partners)));
    }

    protected function deleteUnusedAsset(?string $oldPath, ?string $currentPath): void
    {
        $oldPath = $this->nullableString($oldPath);
        $currentPath = $this->nullableString($currentPath);

        if (
            $oldPath === null
            || $oldPath === $currentPath
            || Str::startsWith($oldPath, ['http://', 'https://'])
            || ! Storage::disk('public')->exists($oldPath)
        ) {
            return;
        }

        Storage::disk('public')->delete($oldPath);
    }

    protected function deleteUnusedAssets(array $oldPaths, array $currentPaths): void
    {
        $currentPaths = array_values(array_filter(array_map(fn ($path): ?string => $this->nullableString($path), $currentPaths)));

        foreach ($oldPaths as $oldPath) {
            $oldPath = $this->nullableString($oldPath);

            if (
                $oldPath === null
                || in_array($oldPath, $currentPaths, true)
                || Str::startsWith($oldPath, ['http://', 'https://'])
                || ! Storage::disk('public')->exists($oldPath)
            ) {
                continue;
            }

            Storage::disk('public')->delete($oldPath);
        }
    }

    protected function externalAssetPath(mixed $value): ?string
    {
        $value = $this->nullableString($value);

        if ($value === null || ! Str::startsWith($value, ['http://', 'https://'])) {
            return null;
        }

        return $value;
    }

    protected function normalizeSettingValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    protected function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $string = trim((string) $value);

        return $string === '' ? null : $string;
    }

    protected function containsSensitiveSettings(array $settings): bool
    {
        foreach (array_keys($settings) as $key) {
            $normalizedKey = strtolower((string) $key);

            if (
                str_contains($normalizedKey, 'token')
                || str_contains($normalizedKey, 'secret')
                || str_contains($normalizedKey, 'password')
                || str_contains($normalizedKey, 'bearer')
                || str_contains($normalizedKey, 'api_key')
            ) {
                return true;
            }
        }

        return false;
    }

    protected function assertPackageVersion(array $package): void
    {
        $version = (int) ($package['version'] ?? 0);

        if ($version !== self::PACKAGE_VERSION) {
            throw new InvalidArgumentException('This transfer file was created with an unsupported package version.');
        }
    }

    protected function resolveUser(?string $email, ?User $fallbackUser, string $context): User
    {
        if ($email !== null) {
            $user = User::query()->where('email', $email)->first();

            if ($user) {
                return $user;
            }
        }

        if ($fallbackUser) {
            return $fallbackUser;
        }

        throw new InvalidArgumentException('Unable to resolve a user for the imported '.$context.'.');
    }

    protected function learningLessonPayloads(array $package): array
    {
        $lessons = [];

        foreach ((array) ($package['courses'] ?? []) as $coursePayload) {
            if (! is_array($coursePayload)) {
                continue;
            }

            foreach ((array) ($coursePayload['lessons'] ?? []) as $lessonPayload) {
                if (is_array($lessonPayload)) {
                    $lessons[] = $lessonPayload;
                }
            }
        }

        return $lessons;
    }

    protected function summarizeSiteContentPackage(array $package): array
    {
        $packages = $package['packages'] ?? [];

        if (! is_array($packages)) {
            throw new InvalidArgumentException('The full site content package is missing its nested packages.');
        }

        $childSummaries = array_values(array_map(
            fn (array $childPackage): array => $this->summarizePackage($childPackage),
            array_filter($packages, 'is_array')
        ));

        $itemCount = array_sum(array_column($childSummaries, 'item_count'));

        return [
            'type' => self::BUNDLE_SITE,
            'label' => 'Full Site Content',
            'item_count' => $itemCount,
            'summary' => 'Full site snapshot with '.$itemCount.' transferable records across '.count($childSummaries).' bundles',
            'details' => [
                'bundles' => $childSummaries,
            ],
        ];
    }
}
