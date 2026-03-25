<?php

namespace App\Livewire\Admin;

use App\Models\Page;
use App\Support\AdminPermissions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class PageManager extends AdminComponent
{
    use WithFileUploads, WithPagination;

    protected array $adminAbilities = [AdminPermissions::MANAGE_PAGES];

    public $pageId;

    public $showForm = false;

    public $editing = false;

    public $title;

    public $slug;

    public $content;

    public $meta_description;

    public $status = 'published';

    public $show_media_sidebar = false;

    public $featured_image;

    public $existingMetaImage;

    public $search = '';

    public $showPreview = false;

    public array $homeSections = [];

    public array $aboutSections = [];

    public array $programsSections = [];

    public array $donationsSections = [];

    public array $contactSections = [];

    public array $partnerLogoUploads = [];

    public array $partnerLogoPathsToDelete = [];

    public array $testimonialImageUploads = [];

    public array $testimonialImagePathsToDelete = [];

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->homeSections = Page::defaultHomeSections();
        $this->aboutSections = Page::defaultAboutSections();
        $this->programsSections = Page::defaultProgramsSections();
        $this->donationsSections = Page::defaultDonationsSections();
        $this->contactSections = Page::defaultContactSections();
    }

    public function render()
    {
        $pages = Page::when($this->search, function ($query) {
            return $query->where('title', 'like', '%'.$this->search.'%')
                ->orWhere('slug', 'like', '%'.$this->search.'%');
        })
            ->orderBy('title')
            ->paginate(10);

        return view('livewire.admin.page-manager', [
            'pages' => $pages,
        ])->layout('layouts.admin')
            ->title('Page Management');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->editing = false;
    }

    public function store()
    {
        $this->persistPage();
    }

    public function edit(int $id)
    {
        $page = Page::findOrFail($id);

        $this->pageId = $page->id;
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->content = $page->content;
        $this->meta_description = $page->meta_description;
        $this->status = $page->status;
        $this->show_media_sidebar = (bool) $page->show_media_sidebar;
        $this->featured_image = null;
        $this->existingMetaImage = $page->meta_image;
        $this->homeSections = $page->slug === 'home'
            ? Page::mergeHomeSections($page->sections)
            : Page::defaultHomeSections();
        $this->aboutSections = $page->slug === 'about'
            ? Page::mergeAboutSections($page->sections)
            : Page::defaultAboutSections();
        $this->programsSections = $page->slug === 'programs'
            ? Page::mergeProgramsSections($page->sections)
            : Page::defaultProgramsSections();
        $this->donationsSections = $page->slug === 'donations'
            ? Page::mergeDonationsSections($page->sections)
            : Page::defaultDonationsSections();
        $this->contactSections = $page->slug === 'contact'
            ? Page::mergeContactSections($page->sections)
            : Page::defaultContactSections();
        $this->partnerLogoUploads = [];
        $this->partnerLogoPathsToDelete = [];
        $this->testimonialImageUploads = [];
        $this->testimonialImagePathsToDelete = [];
        $this->showPreview = false;
        $this->showForm = true;
        $this->editing = true;
    }

    public function update()
    {
        $this->persistPage();
    }

    public function delete(int $id)
    {
        $page = Page::findOrFail($id);

        if ($page->meta_image) {
            Storage::disk('public')->delete($page->meta_image);
        }

        $page->delete();
        session()->flash('success', 'Page deleted successfully.');
    }

    public function toggleStatus(int $id)
    {
        $page = Page::findOrFail($id);
        $page->update([
            'status' => $page->status === 'published' ? 'draft' : 'published',
        ]);
        session()->flash('success', 'Page status updated.');
    }

    public function updatedTitle()
    {
        $this->slug = Str::slug($this->title);
    }

    public function updatedSlug($value): void
    {
        if (Str::lower((string) $value) === 'home') {
            $this->homeSections = Page::mergeHomeSections($this->homeSections);
        }

        if (Str::lower((string) $value) === 'about') {
            $this->aboutSections = Page::mergeAboutSections($this->aboutSections);
        }

        if (Str::lower((string) $value) === 'programs') {
            $this->programsSections = Page::mergeProgramsSections($this->programsSections);
        }

        if (Str::lower((string) $value) === 'donations') {
            $this->donationsSections = Page::mergeDonationsSections($this->donationsSections);
        }

        if (Str::lower((string) $value) === 'contact') {
            $this->contactSections = Page::mergeContactSections($this->contactSections);
        }
    }

    public function submit(): void
    {
        if ($this->status === 'published') {
            $this->openPreview();

            return;
        }

        $this->persistPage();
    }

    public function openPreview(): void
    {
        $this->validatePageData();
        $this->showPreview = true;
    }

    public function closePreview(): void
    {
        $this->showPreview = false;
    }

    public function confirmPreviewAction(): void
    {
        $this->persistPage();
    }

    public function getSubmitLabelProperty(): string
    {
        if ($this->status === 'published') {
            return $this->editing ? 'Preview & Update' : 'Preview & Publish';
        }

        return $this->editing ? 'Update Page' : 'Create Page';
    }

    public function getPreviewActionLabelProperty(): string
    {
        if ($this->status === 'published') {
            return $this->editing ? 'Update Published Page' : 'Publish Page';
        }

        return $this->editing ? 'Save Changes' : 'Create Page';
    }

    public function getIsHomepageProperty(): bool
    {
        return Str::lower((string) $this->slug) === 'home';
    }

    public function getIsAboutPageProperty(): bool
    {
        return Str::lower((string) $this->slug) === 'about';
    }

    public function getIsProgramsPageProperty(): bool
    {
        return Str::lower((string) $this->slug) === 'programs';
    }

    public function getIsDonationsPageProperty(): bool
    {
        return Str::lower((string) $this->slug) === 'donations';
    }

    public function getIsContactPageProperty(): bool
    {
        return Str::lower((string) $this->slug) === 'contact';
    }

    public function addTrustPartner(): void
    {
        $partners = $this->homeSections['trust']['partners'] ?? [];
        $partners[] = [
            'name' => '',
            'website_url' => '',
            'logo_path' => null,
        ];

        $this->homeSections['trust']['partners'] = array_values($partners);
    }

    public function removeTrustPartner(int $index): void
    {
        $logoPath = data_get($this->homeSections, "trust.partners.{$index}.logo_path");

        if (filled($logoPath) && ! Str::startsWith($logoPath, ['http://', 'https://'])) {
            $this->partnerLogoPathsToDelete[] = $logoPath;
        }

        unset($this->homeSections['trust']['partners'][$index], $this->partnerLogoUploads[$index]);

        $this->homeSections['trust']['partners'] = array_values($this->homeSections['trust']['partners'] ?? []);
        $this->partnerLogoUploads = array_values($this->partnerLogoUploads);
    }

    public function removeTrustPartnerLogo(int $index): void
    {
        $logoPath = data_get($this->homeSections, "trust.partners.{$index}.logo_path");

        if (filled($logoPath) && ! Str::startsWith($logoPath, ['http://', 'https://'])) {
            $this->partnerLogoPathsToDelete[] = $logoPath;
        }

        data_set($this->homeSections, "trust.partners.{$index}.logo_path", null);
        unset($this->partnerLogoUploads[$index]);
    }

    public function addHomepageTestimonial(): void
    {
        $items = $this->homeSections['testimonials']['items'] ?? [];
        $items[] = [
            'quote' => '',
            'name' => '',
            'role' => '',
            'image_path' => null,
        ];

        $this->homeSections['testimonials']['items'] = array_values($items);
    }

    public function removeHomepageTestimonial(int $index): void
    {
        $imagePath = data_get($this->homeSections, "testimonials.items.{$index}.image_path");

        if (filled($imagePath) && ! Str::startsWith($imagePath, ['http://', 'https://'])) {
            $this->testimonialImagePathsToDelete[] = $imagePath;
        }

        unset($this->testimonialImageUploads[$index]);
        unset($this->homeSections['testimonials']['items'][$index]);

        $this->homeSections['testimonials']['items'] = array_values($this->homeSections['testimonials']['items'] ?? []);
        $this->testimonialImageUploads = array_values($this->testimonialImageUploads);
    }

    public function removeHomepageTestimonialImage(int $index): void
    {
        $imagePath = data_get($this->homeSections, "testimonials.items.{$index}.image_path");

        if (filled($imagePath) && ! Str::startsWith($imagePath, ['http://', 'https://'])) {
            $this->testimonialImagePathsToDelete[] = $imagePath;
        }

        data_set($this->homeSections, "testimonials.items.{$index}.image_path", null);
        unset($this->testimonialImageUploads[$index]);
    }

    public function cancel()
    {
        $this->resetForm();
    }

    private function persistPage(): void
    {
        $validated = $this->validatePageData();
        $imagePath = $this->featured_image?->store('pages', 'public');
        $sections = $validated['sections'] ?? null;

        if ($this->editing) {
            $page = Page::findOrFail($this->pageId);

            if ($this->isHomepage) {
                $sections = $this->prepareHomepageSections($sections);
            } elseif ($this->isAboutPage) {
                $sections = Page::mergeAboutSections($sections);
            } elseif ($this->isProgramsPage) {
                $sections = Page::mergeProgramsSections($sections);
            } elseif ($this->isDonationsPage) {
                $sections = Page::mergeDonationsSections($sections);
            } elseif ($this->isContactPage) {
                $sections = Page::mergeContactSections($sections);
            }

            if ($imagePath && $page->meta_image) {
                Storage::disk('public')->delete($page->meta_image);
            }

            $page->update([
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'content' => $validated['content'],
                'meta_description' => $validated['meta_description'],
                'status' => $validated['status'],
                'show_media_sidebar' => (bool) $validated['show_media_sidebar'],
                'meta_image' => $imagePath ?: $page->meta_image,
            ]);

            if ($this->isHomepage || $this->isAboutPage || $this->isProgramsPage || $this->isDonationsPage || $this->isContactPage) {
                $page->update([
                    'sections' => $sections,
                ]);
            }

            session()->flash('success', 'Page updated successfully.');
        } else {
            if ($this->isHomepage) {
                $sections = $this->prepareHomepageSections($sections);
            } elseif ($this->isAboutPage) {
                $sections = Page::mergeAboutSections($sections);
            } elseif ($this->isProgramsPage) {
                $sections = Page::mergeProgramsSections($sections);
            } elseif ($this->isDonationsPage) {
                $sections = Page::mergeDonationsSections($sections);
            } elseif ($this->isContactPage) {
                $sections = Page::mergeContactSections($sections);
            }

            $payload = [
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'content' => $validated['content'],
                'meta_description' => $validated['meta_description'],
                'status' => $validated['status'],
                'show_media_sidebar' => (bool) $validated['show_media_sidebar'],
                'meta_image' => $imagePath,
            ];

            if ($this->isHomepage || $this->isAboutPage || $this->isProgramsPage || $this->isDonationsPage || $this->isContactPage) {
                $payload['sections'] = $sections;
            }

            Page::create($payload);

            session()->flash('success', 'Page created successfully.');
        }

        $this->resetForm();
    }

    private function validatePageData(): array
    {
        $sections = null;

        if ($this->isHomepage) {
            $sections = Page::mergeHomeSections($this->homeSections);
        } elseif ($this->isAboutPage) {
            $sections = Page::mergeAboutSections($this->aboutSections);
        } elseif ($this->isProgramsPage) {
            $sections = Page::mergeProgramsSections($this->programsSections);
        } elseif ($this->isDonationsPage) {
            $sections = Page::mergeDonationsSections($this->donationsSections);
        } elseif ($this->isContactPage) {
            $sections = Page::mergeContactSections($this->contactSections);
        }

        $validator = Validator::make(
            [
                'title' => $this->title,
                'slug' => $this->slug,
                'content' => $this->content,
                'meta_description' => $this->meta_description,
                'status' => $this->status,
                'show_media_sidebar' => $this->show_media_sidebar,
                'featured_image' => $this->featured_image,
                'sections' => $sections,
                'partner_logo_uploads' => $this->partnerLogoUploads,
                'testimonial_image_uploads' => $this->testimonialImageUploads,
            ],
            array_merge([
                'title' => 'required|string|max:255',
                'slug' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('pages', 'slug')->ignore($this->editing ? $this->pageId : null),
                ],
                'content' => 'required|string',
                'meta_description' => 'nullable|string|max:255',
                'status' => 'required|in:draft,published,archived',
                'show_media_sidebar' => 'boolean',
                'featured_image' => 'nullable|image|max:2048',
            ], $this->structuredSectionRules())
        );

        $validator->after(function ($validator): void {
            $plainText = html_entity_decode(strip_tags((string) $this->content), ENT_QUOTES | ENT_HTML5, 'UTF-8');

            if (trim(str_replace("\xc2\xa0", ' ', $plainText)) === '') {
                $validator->errors()->add('content', 'Content cannot be empty.');
            }
        });

        try {
            return $validator->validate();
        } catch (ValidationException $exception) {
            $this->showPreview = false;

            throw $exception;
        }
    }

    private function resetForm(): void
    {
        $this->reset([
            'pageId',
            'title',
            'slug',
            'content',
            'meta_description',
            'show_media_sidebar',
            'featured_image',
            'existingMetaImage',
            'showForm',
            'editing',
            'showPreview',
            'aboutSections',
            'programsSections',
            'donationsSections',
            'contactSections',
            'partnerLogoUploads',
            'partnerLogoPathsToDelete',
            'testimonialImageUploads',
            'testimonialImagePathsToDelete',
        ]);
        $this->homeSections = Page::defaultHomeSections();
        $this->aboutSections = Page::defaultAboutSections();
        $this->programsSections = Page::defaultProgramsSections();
        $this->donationsSections = Page::defaultDonationsSections();
        $this->contactSections = Page::defaultContactSections();
        $this->status = 'published';
        $this->show_media_sidebar = false;
    }

    private function structuredSectionRules(): array
    {
        if ($this->isHomepage) {
            return $this->homeSectionRules();
        }

        if ($this->isAboutPage) {
            return $this->aboutSectionRules();
        }

        if ($this->isProgramsPage) {
            return $this->programsSectionRules();
        }

        if ($this->isDonationsPage) {
            return $this->donationsSectionRules();
        }

        if ($this->isContactPage) {
            return $this->contactSectionRules();
        }

        return [];
    }

    private function homeSectionRules(): array
    {
        return [
            'sections.landing.eyebrow' => 'nullable|string|max:120',
            'sections.landing.headline' => 'nullable|string|max:255',
            'sections.landing.subheadline' => 'nullable|string|max:500',
            'sections.landing.primary_cta_label' => 'nullable|string|max:80',
            'sections.landing.primary_cta_url' => 'nullable|string|max:255',
            'sections.landing.secondary_cta_label' => 'nullable|string|max:80',
            'sections.landing.secondary_cta_url' => 'nullable|string|max:255',
            'sections.landing.hero_image_alt' => 'nullable|string|max:255',
            'sections.identity.mission_title' => 'nullable|string|max:80',
            'sections.identity.mission_body' => 'nullable|string|max:500',
            'sections.identity.vision_title' => 'nullable|string|max:80',
            'sections.identity.vision_body' => 'nullable|string|max:500',
            'sections.identity.approach_title' => 'nullable|string|max:80',
            'sections.identity.approach_body' => 'nullable|string|max:500',
            'sections.analytics.eyebrow' => 'nullable|string|max:80',
            'sections.analytics.title' => 'nullable|string|max:255',
            'sections.analytics.intro' => 'nullable|string|max:500',
            'sections.analytics.cta_label' => 'nullable|string|max:80',
            'sections.analytics.cta_url' => 'nullable|string|max:255',
            'sections.analytics.cards' => 'array|size:6',
            'sections.analytics.cards.*.value' => 'nullable|string|max:80',
            'sections.analytics.cards.*.label' => 'nullable|string|max:120',
            'sections.analytics.cards.*.description' => 'nullable|string|max:300',
            'sections.testimonials.eyebrow' => 'nullable|string|max:80',
            'sections.testimonials.title' => 'nullable|string|max:255',
            'sections.testimonials.intro' => 'nullable|string|max:500',
            'sections.testimonials.items' => 'array|max:12',
            'sections.testimonials.items.*.quote' => 'nullable|string|max:500',
            'sections.testimonials.items.*.name' => 'nullable|string|max:120',
            'sections.testimonials.items.*.role' => 'nullable|string|max:120',
            'sections.testimonials.items.*.image_path' => 'nullable|string|max:255',
            'sections.services.eyebrow' => 'nullable|string|max:80',
            'sections.services.title' => 'nullable|string|max:255',
            'sections.services.intro' => 'nullable|string|max:500',
            'sections.services.items' => 'array|size:3',
            'sections.services.items.*.eyebrow' => 'nullable|string|max:80',
            'sections.services.items.*.title' => 'nullable|string|max:120',
            'sections.services.items.*.description' => 'nullable|string|max:500',
            'sections.services.items.*.cta_label' => 'nullable|string|max:80',
            'sections.services.items.*.cta_url' => 'nullable|string|max:255',
            'sections.impact.eyebrow' => 'nullable|string|max:80',
            'sections.impact.title' => 'nullable|string|max:255',
            'sections.impact.body' => 'nullable|string|max:500',
            'sections.impact.quote' => 'nullable|string|max:500',
            'sections.impact.quote_author' => 'nullable|string|max:120',
            'sections.impact.quote_role' => 'nullable|string|max:120',
            'sections.trust.visible' => 'boolean',
            'sections.trust.eyebrow' => 'nullable|string|max:80',
            'sections.trust.title' => 'nullable|string|max:255',
            'sections.trust.body' => 'nullable|string|max:500',
            'sections.trust.story_visible' => 'boolean',
            'sections.trust.story_eyebrow' => 'nullable|string|max:80',
            'sections.trust.story_title' => 'nullable|string|max:255',
            'sections.trust.story_body' => 'nullable|string|max:500',
            'sections.trust.story_name' => 'nullable|string|max:120',
            'sections.trust.story_role' => 'nullable|string|max:120',
            'sections.trust.partners_visible' => 'boolean',
            'sections.trust.partners_title' => 'nullable|string|max:120',
            'sections.trust.partners' => 'array|max:12',
            'sections.trust.partners.*.name' => 'nullable|string|max:120',
            'sections.trust.partners.*.website_url' => 'nullable|string|max:255',
            'sections.trust.partners.*.logo_path' => 'nullable|string|max:255',
            'partner_logo_uploads' => 'array',
            'partner_logo_uploads.*' => 'nullable|image|max:2048',
            'testimonial_image_uploads' => 'array',
            'testimonial_image_uploads.*' => 'nullable|image|max:2048',
            'sections.accessibility.eyebrow' => 'nullable|string|max:80',
            'sections.accessibility.title' => 'nullable|string|max:255',
            'sections.accessibility.body' => 'nullable|string|max:500',
            'sections.accessibility.items' => 'array|size:4',
            'sections.accessibility.items.*.title' => 'nullable|string|max:120',
            'sections.accessibility.items.*.description' => 'nullable|string|max:400',
            'sections.involvement.eyebrow' => 'nullable|string|max:80',
            'sections.involvement.title' => 'nullable|string|max:255',
            'sections.involvement.intro' => 'nullable|string|max:500',
            'sections.involvement.items' => 'array|size:4',
            'sections.involvement.items.*.title' => 'nullable|string|max:120',
            'sections.involvement.items.*.description' => 'nullable|string|max:400',
            'sections.involvement.items.*.cta_label' => 'nullable|string|max:80',
            'sections.involvement.items.*.cta_url' => 'nullable|string|max:255',
            'sections.closing_cta.title' => 'nullable|string|max:255',
            'sections.closing_cta.body' => 'nullable|string|max:500',
            'sections.closing_cta.primary_label' => 'nullable|string|max:80',
            'sections.closing_cta.primary_url' => 'nullable|string|max:255',
            'sections.closing_cta.secondary_label' => 'nullable|string|max:80',
            'sections.closing_cta.secondary_url' => 'nullable|string|max:255',
        ];
    }

    private function aboutSectionRules(): array
    {
        return [
            'sections.hero.eyebrow' => 'nullable|string|max:120',
            'sections.hero.headline' => 'nullable|string|max:255',
            'sections.hero.subheadline' => 'nullable|string|max:500',
            'sections.hero.primary_cta_label' => 'nullable|string|max:80',
            'sections.hero.primary_cta_url' => 'nullable|string|max:255',
            'sections.hero.secondary_cta_label' => 'nullable|string|max:80',
            'sections.hero.secondary_cta_url' => 'nullable|string|max:255',
            'sections.hero.image_alt' => 'nullable|string|max:255',
            'sections.story.eyebrow' => 'nullable|string|max:80',
            'sections.story.title' => 'nullable|string|max:255',
            'sections.story.highlight' => 'nullable|string|max:500',
            'sections.identity.mission_title' => 'nullable|string|max:80',
            'sections.identity.mission_body' => 'nullable|string|max:500',
            'sections.identity.vision_title' => 'nullable|string|max:80',
            'sections.identity.vision_body' => 'nullable|string|max:500',
            'sections.identity.values_title' => 'nullable|string|max:80',
            'sections.identity.values_body' => 'nullable|string|max:500',
            'sections.commitments.eyebrow' => 'nullable|string|max:80',
            'sections.commitments.title' => 'nullable|string|max:255',
            'sections.commitments.intro' => 'nullable|string|max:500',
            'sections.commitments.items' => 'array|size:3',
            'sections.commitments.items.*.title' => 'nullable|string|max:120',
            'sections.commitments.items.*.description' => 'nullable|string|max:400',
            'sections.quote.eyebrow' => 'nullable|string|max:80',
            'sections.quote.text' => 'nullable|string|max:500',
            'sections.quote.author' => 'nullable|string|max:120',
            'sections.quote.role' => 'nullable|string|max:120',
            'sections.closing_cta.title' => 'nullable|string|max:255',
            'sections.closing_cta.body' => 'nullable|string|max:500',
            'sections.closing_cta.primary_label' => 'nullable|string|max:80',
            'sections.closing_cta.primary_url' => 'nullable|string|max:255',
            'sections.closing_cta.secondary_label' => 'nullable|string|max:80',
            'sections.closing_cta.secondary_url' => 'nullable|string|max:255',
        ];
    }

    private function programsSectionRules(): array
    {
        return [
            'sections.hero.eyebrow' => 'nullable|string|max:120',
            'sections.hero.headline' => 'nullable|string|max:255',
            'sections.hero.subheadline' => 'nullable|string|max:500',
            'sections.hero.primary_cta_label' => 'nullable|string|max:80',
            'sections.hero.primary_cta_url' => 'nullable|string|max:255',
            'sections.hero.secondary_cta_label' => 'nullable|string|max:80',
            'sections.hero.secondary_cta_url' => 'nullable|string|max:255',
            'sections.hero.image_alt' => 'nullable|string|max:255',
            'sections.story.eyebrow' => 'nullable|string|max:80',
            'sections.story.title' => 'nullable|string|max:255',
            'sections.story.highlight' => 'nullable|string|max:500',
            'sections.pillars.eyebrow' => 'nullable|string|max:80',
            'sections.pillars.title' => 'nullable|string|max:255',
            'sections.pillars.intro' => 'nullable|string|max:500',
            'sections.pillars.items' => 'array|size:4',
            'sections.pillars.items.*.eyebrow' => 'nullable|string|max:80',
            'sections.pillars.items.*.title' => 'nullable|string|max:120',
            'sections.pillars.items.*.description' => 'nullable|string|max:500',
            'sections.pillars.items.*.cta_label' => 'nullable|string|max:80',
            'sections.pillars.items.*.cta_url' => 'nullable|string|max:255',
            'sections.audiences.eyebrow' => 'nullable|string|max:80',
            'sections.audiences.title' => 'nullable|string|max:255',
            'sections.audiences.intro' => 'nullable|string|max:500',
            'sections.audiences.items' => 'array|size:4',
            'sections.audiences.items.*.title' => 'nullable|string|max:120',
            'sections.audiences.items.*.description' => 'nullable|string|max:400',
            'sections.outcomes.eyebrow' => 'nullable|string|max:80',
            'sections.outcomes.title' => 'nullable|string|max:255',
            'sections.outcomes.body' => 'nullable|string|max:500',
            'sections.outcomes.quote' => 'nullable|string|max:500',
            'sections.outcomes.quote_author' => 'nullable|string|max:120',
            'sections.outcomes.quote_role' => 'nullable|string|max:120',
            'sections.closing_cta.title' => 'nullable|string|max:255',
            'sections.closing_cta.body' => 'nullable|string|max:500',
            'sections.closing_cta.primary_label' => 'nullable|string|max:80',
            'sections.closing_cta.primary_url' => 'nullable|string|max:255',
            'sections.closing_cta.secondary_label' => 'nullable|string|max:80',
            'sections.closing_cta.secondary_url' => 'nullable|string|max:255',
        ];
    }

    private function donationsSectionRules(): array
    {
        return [
            'sections.hero.eyebrow' => 'nullable|string|max:120',
            'sections.hero.headline' => 'nullable|string|max:255',
            'sections.hero.subheadline' => 'nullable|string|max:500',
            'sections.hero.primary_cta_label' => 'nullable|string|max:80',
            'sections.hero.primary_cta_url' => 'nullable|string|max:255',
            'sections.hero.secondary_cta_label' => 'nullable|string|max:80',
            'sections.hero.secondary_cta_url' => 'nullable|string|max:255',
            'sections.hero.image_alt' => 'nullable|string|max:255',
            'sections.story.eyebrow' => 'nullable|string|max:80',
            'sections.story.title' => 'nullable|string|max:255',
            'sections.story.highlight' => 'nullable|string|max:500',
            'sections.bank.eyebrow' => 'nullable|string|max:80',
            'sections.bank.title' => 'nullable|string|max:255',
            'sections.bank.body' => 'nullable|string|max:500',
            'sections.bank.reference_note' => 'nullable|string|max:500',
            'sections.bank.accounts' => 'array|size:3',
            'sections.bank.accounts.*.currency_label' => 'nullable|string|max:80',
            'sections.bank.accounts.*.account_name' => 'nullable|string|max:120',
            'sections.bank.accounts.*.bank_name' => 'nullable|string|max:120',
            'sections.bank.accounts.*.account_number' => 'nullable|string|max:120',
            'sections.bank.accounts.*.routing_code' => 'nullable|string|max:120',
            'sections.bank.accounts.*.note' => 'nullable|string|max:500',
            'sections.acknowledgement.eyebrow' => 'nullable|string|max:80',
            'sections.acknowledgement.title' => 'nullable|string|max:255',
            'sections.acknowledgement.body' => 'nullable|string|max:500',
            'sections.acknowledgement.email_label' => 'nullable|string|max:80',
            'sections.acknowledgement.email_address' => 'nullable|string|max:255',
            'sections.acknowledgement.email_subject' => 'nullable|string|max:120',
            'sections.acknowledgement.email_message' => 'nullable|string|max:700',
            'sections.acknowledgement.sms_label' => 'nullable|string|max:80',
            'sections.acknowledgement.sms_number' => 'nullable|string|max:60',
            'sections.acknowledgement.sms_message' => 'nullable|string|max:320',
            'sections.acknowledgement.tip' => 'nullable|string|max:500',
            'sections.impact.eyebrow' => 'nullable|string|max:80',
            'sections.impact.title' => 'nullable|string|max:255',
            'sections.impact.intro' => 'nullable|string|max:500',
            'sections.impact.items' => 'array|size:3',
            'sections.impact.items.*.amount' => 'nullable|string|max:80',
            'sections.impact.items.*.title' => 'nullable|string|max:120',
            'sections.impact.items.*.description' => 'nullable|string|max:400',
            'sections.closing_cta.title' => 'nullable|string|max:255',
            'sections.closing_cta.body' => 'nullable|string|max:500',
            'sections.closing_cta.primary_label' => 'nullable|string|max:80',
            'sections.closing_cta.primary_url' => 'nullable|string|max:255',
            'sections.closing_cta.secondary_label' => 'nullable|string|max:80',
            'sections.closing_cta.secondary_url' => 'nullable|string|max:255',
        ];
    }

    private function contactSectionRules(): array
    {
        return [
            'sections.hero.eyebrow' => 'nullable|string|max:120',
            'sections.hero.headline' => 'nullable|string|max:255',
            'sections.hero.subheadline' => 'nullable|string|max:500',
            'sections.hero.primary_cta_label' => 'nullable|string|max:80',
            'sections.hero.primary_cta_url' => 'nullable|string|max:255',
            'sections.hero.secondary_cta_label' => 'nullable|string|max:80',
            'sections.hero.secondary_cta_url' => 'nullable|string|max:255',
            'sections.hero.image_alt' => 'nullable|string|max:255',
            'sections.intro.eyebrow' => 'nullable|string|max:80',
            'sections.intro.title' => 'nullable|string|max:255',
            'sections.intro.highlight' => 'nullable|string|max:500',
            'sections.pathways.eyebrow' => 'nullable|string|max:80',
            'sections.pathways.title' => 'nullable|string|max:255',
            'sections.pathways.intro' => 'nullable|string|max:500',
            'sections.pathways.items' => 'array|size:3',
            'sections.pathways.items.*.title' => 'nullable|string|max:120',
            'sections.pathways.items.*.description' => 'nullable|string|max:500',
            'sections.pathways.items.*.cta_label' => 'nullable|string|max:80',
            'sections.pathways.items.*.cta_url' => 'nullable|string|max:255',
            'sections.contact_info.eyebrow' => 'nullable|string|max:80',
            'sections.contact_info.title' => 'nullable|string|max:255',
            'sections.contact_info.body' => 'nullable|string|max:500',
            'sections.contact_info.email_title' => 'nullable|string|max:80',
            'sections.contact_info.email_body' => 'nullable|string|max:300',
            'sections.contact_info.phone_title' => 'nullable|string|max:80',
            'sections.contact_info.phone_body' => 'nullable|string|max:300',
            'sections.contact_info.address_title' => 'nullable|string|max:80',
            'sections.contact_info.address_body' => 'nullable|string|max:300',
            'sections.form.eyebrow' => 'nullable|string|max:80',
            'sections.form.title' => 'nullable|string|max:255',
            'sections.form.intro' => 'nullable|string|max:500',
            'sections.form.response_promise' => 'nullable|string|max:400',
            'sections.form.accessibility_note' => 'nullable|string|max:400',
            'sections.closing_cta.title' => 'nullable|string|max:255',
            'sections.closing_cta.body' => 'nullable|string|max:500',
            'sections.closing_cta.primary_label' => 'nullable|string|max:80',
            'sections.closing_cta.primary_url' => 'nullable|string|max:255',
            'sections.closing_cta.secondary_label' => 'nullable|string|max:80',
            'sections.closing_cta.secondary_url' => 'nullable|string|max:255',
        ];
    }

    private function prepareHomepageSections(?array $sections): array
    {
        $sections = Page::mergeHomeSections($sections);
        $partners = [];

        foreach ($sections['trust']['partners'] ?? [] as $index => $partner) {
            $name = trim((string) ($partner['name'] ?? ''));
            $websiteUrl = trim((string) ($partner['website_url'] ?? ''));
            $logoPath = $partner['logo_path'] ?? null;
            $upload = $this->partnerLogoUploads[$index] ?? null;

            if ($upload) {
                if (filled($logoPath) && ! Str::startsWith($logoPath, ['http://', 'https://'])) {
                    Storage::disk('public')->delete($logoPath);
                }

                $logoPath = $upload->store('pages/partners', 'public');
            }

            if ($name === '' && $websiteUrl === '' && blank($logoPath)) {
                continue;
            }

            $partners[] = [
                'name' => $name,
                'website_url' => $websiteUrl ?: null,
                'logo_path' => $logoPath,
            ];
        }

        $sections['trust']['partners'] = $partners;

        $testimonials = [];

        foreach ($sections['testimonials']['items'] ?? [] as $index => $testimonial) {
            $quote = trim((string) ($testimonial['quote'] ?? ''));
            $name = trim((string) ($testimonial['name'] ?? ''));
            $role = trim((string) ($testimonial['role'] ?? ''));
            $imagePath = trim((string) ($testimonial['image_path'] ?? ''));
            $upload = $this->testimonialImageUploads[$index] ?? null;

            if ($upload) {
                if (filled($imagePath) && ! Str::startsWith($imagePath, ['http://', 'https://'])) {
                    Storage::disk('public')->delete($imagePath);
                }

                $imagePath = $upload->store('pages/testimonials', 'public');
            }

            if ($quote === '' && $name === '' && $role === '' && $imagePath === '') {
                continue;
            }

            $testimonials[] = [
                'quote' => $quote,
                'name' => $name,
                'role' => $role,
                'image_path' => $imagePath ?: null,
            ];
        }

        $sections['testimonials']['items'] = $testimonials;

        foreach (array_unique($this->partnerLogoPathsToDelete) as $path) {
            if (
                filled($path)
                && ! in_array($path, array_column($partners, 'logo_path'), true)
                && ! Str::startsWith($path, ['http://', 'https://'])
            ) {
                Storage::disk('public')->delete($path);
            }
        }

        foreach (array_unique($this->testimonialImagePathsToDelete) as $path) {
            if (
                filled($path)
                && ! in_array($path, array_column($testimonials, 'image_path'), true)
                && ! Str::startsWith($path, ['http://', 'https://'])
            ) {
                Storage::disk('public')->delete($path);
            }
        }

        return $sections;
    }
}
