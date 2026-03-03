<?php

namespace App\Livewire\Admin;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class SiteSettingsManager extends Component
{
    use WithFileUploads;

    public $site_name = '';
    public $footer_tagline = '';
    public $footer_phone = '';
    public $footer_email = '';
    public $footer_address = '';

    public $logo = null;
    public $favicon = null;
    public $logo_path = '';
    public $favicon_path = '';

    public function mount(): void
    {
        $this->site_name = SiteSetting::getValue('site_name', 'Friends of the Deaf Foundation') ?? '';
        $this->footer_tagline = SiteSetting::getValue('footer_tagline', '') ?? '';
        $this->footer_phone = SiteSetting::getValue('footer_phone', '') ?? '';
        $this->footer_email = SiteSetting::getValue('footer_email', '') ?? '';
        $this->footer_address = SiteSetting::getValue('footer_address', '') ?? '';
        $this->logo_path = SiteSetting::getValue('site_logo_path', '') ?? '';
        $this->favicon_path = SiteSetting::getValue('site_favicon_path', '') ?? '';
    }

    public function saveFooterSettings(): void
    {
        $validated = $this->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'footer_tagline' => ['nullable', 'string', 'max:1000'],
            'footer_phone' => ['nullable', 'string', 'max:255'],
            'footer_email' => ['nullable', 'email', 'max:255'],
            'footer_address' => ['nullable', 'string', 'max:1000'],
        ]);

        SiteSetting::setValue('site_name', $validated['site_name']);
        SiteSetting::setValue('footer_tagline', $validated['footer_tagline'] ?? '');
        SiteSetting::setValue('footer_phone', $validated['footer_phone'] ?? '');
        SiteSetting::setValue('footer_email', $validated['footer_email'] ?? '');
        SiteSetting::setValue('footer_address', $validated['footer_address'] ?? '');

        session()->flash('success', 'Footer settings updated successfully.');
    }

    public function saveBrandingAssets(): void
    {
        $this->validate([
            'logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:3072'],
            'favicon' => ['nullable', 'file', 'mimes:ico,png', 'max:1024'],
        ]);

        if ($this->logo) {
            $this->replaceAsset('site_logo_path', $this->logo_path, $this->logo->store('settings', 'public'));
        }

        if ($this->favicon) {
            $this->replaceAsset('site_favicon_path', $this->favicon_path, $this->favicon->store('settings', 'public'));
        }

        $this->logo = null;
        $this->favicon = null;
        $this->logo_path = SiteSetting::getValue('site_logo_path', '') ?? '';
        $this->favicon_path = SiteSetting::getValue('site_favicon_path', '') ?? '';

        session()->flash('success', 'Branding assets updated successfully.');
    }

    public function clearLogo(): void
    {
        if ($this->logo_path !== '' && Storage::disk('public')->exists($this->logo_path)) {
            Storage::disk('public')->delete($this->logo_path);
        }

        SiteSetting::setValue('site_logo_path', '');
        $this->logo_path = '';
        session()->flash('success', 'Site logo removed.');
    }

    public function clearFavicon(): void
    {
        if ($this->favicon_path !== '' && Storage::disk('public')->exists($this->favicon_path)) {
            Storage::disk('public')->delete($this->favicon_path);
        }

        SiteSetting::setValue('site_favicon_path', '');
        $this->favicon_path = '';
        session()->flash('success', 'Favicon removed.');
    }

    public function render()
    {
        return view('livewire.admin.site-settings-manager')
            ->layout('layouts.admin')
            ->title('Site Settings');
    }

    private function replaceAsset(string $settingKey, string $currentPath, string $newPath): void
    {
        if ($currentPath !== '' && Storage::disk('public')->exists($currentPath)) {
            Storage::disk('public')->delete($currentPath);
        }

        SiteSetting::setValue($settingKey, $newPath);
    }
}
