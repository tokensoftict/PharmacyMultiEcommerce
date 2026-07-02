<?php

namespace App\Livewire\Backend\Admin\Settings;

use App\Models\AppVersion;
use App\Services\AppUpdate\AppVersionService;
use Livewire\Component;

/**
 * AppVersionManagerComponent
 * ─────────────────────────────────────────────────────────────────────────────
 * Admin UI for managing Android and iOS app version configs.
 *
 * Renders at: livewire.pages.backend.admin.settings.app-version-manager-component
 */
class AppVersionManagerComponent extends Component
{
    // ── Android form fields ───────────────────────────────────────────────────
    public string  $android_version_name   = '';
    public int     $android_version_code   = 0;
    public bool    $android_force_update   = false;
    public string  $android_update_message = '';
    public string  $android_store_url      = '';
    public bool    $android_is_active      = true;

    // ── iOS form fields ───────────────────────────────────────────────────────
    public string  $ios_version_name       = '';
    public int     $ios_version_code       = 0;
    public bool    $ios_force_update       = false;
    public string  $ios_update_message     = '';
    public string  $ios_store_url          = '';
    public bool    $ios_is_active          = true;

    // ── Internal ──────────────────────────────────────────────────────────────
    private AppVersionService $appVersionService;

    public function boot(AppVersionService $appVersionService): void
    {
        $this->appVersionService = $appVersionService;
    }

    public function mount(): void
    {
        $this->loadPlatform('android');
        $this->loadPlatform('ios');
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.pages.backend.admin.settings.app-version-manager-component');
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    /**
     * Save/update the Android version config.
     */
    public function updateAndroid(): void
    {
        $this->validatePlatform('android');

        $this->savePlatform('android', [
            'version_name'   => $this->android_version_name,
            'version_code'   => $this->android_version_code,
            'force_update'   => $this->android_force_update,
            'update_message' => $this->android_update_message,
            'store_url'      => $this->android_store_url,
            'is_active'      => $this->android_is_active,
        ]);

        $this->appVersionService->clearCache('android');
        $this->alert('success', 'Android version settings saved and cache cleared!');
    }

    /**
     * Save/update the iOS version config.
     */
    public function updateIos(): void
    {
        $this->validatePlatform('ios');

        $this->savePlatform('ios', [
            'version_name'   => $this->ios_version_name,
            'version_code'   => $this->ios_version_code,
            'force_update'   => $this->ios_force_update,
            'update_message' => $this->ios_update_message,
            'store_url'      => $this->ios_store_url,
            'is_active'      => $this->ios_is_active,
        ]);

        $this->appVersionService->clearCache('ios');
        $this->alert('success', 'iOS version settings saved and cache cleared!');
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function loadPlatform(string $platform): void
    {
        $record = AppVersion::latestForPlatform($platform);

        if (! $record) {
            return;
        }

        if ($platform === 'android') {
            $this->android_version_name   = $record->version_name;
            $this->android_version_code   = $record->version_code;
            $this->android_force_update   = $record->force_update;
            $this->android_update_message = $record->update_message;
            $this->android_store_url      = $record->store_url ?? '';
            $this->android_is_active      = $record->is_active;
        } else {
            $this->ios_version_name       = $record->version_name;
            $this->ios_version_code       = $record->version_code;
            $this->ios_force_update       = $record->force_update;
            $this->ios_update_message     = $record->update_message;
            $this->ios_store_url          = $record->store_url ?? '';
            $this->ios_is_active          = $record->is_active;
        }
    }

    private function savePlatform(string $platform, array $data): void
    {
        // Deactivate all previous records for this platform
        AppVersion::where('app_type', $platform)->update(['is_active' => false]);

        // Upsert on app_type + version_code to avoid duplicates
        AppVersion::updateOrCreate(
            ['app_type' => $platform, 'version_code' => $data['version_code']],
            array_merge($data, ['app_type' => $platform])
        );
    }

    private function validatePlatform(string $platform): void
    {
        $prefix = $platform === 'android' ? 'android' : 'ios';

        $this->validate([
            "{$prefix}_version_name"   => 'required|string|max:20',
            "{$prefix}_version_code"   => 'required|integer|min:1',
            "{$prefix}_update_message" => 'required|string|max:255',
            "{$prefix}_store_url"      => 'nullable|url|max:500',
        ], [], [
            "{$prefix}_version_name"   => 'version name',
            "{$prefix}_version_code"   => 'version code',
            "{$prefix}_update_message" => 'update message',
            "{$prefix}_store_url"      => 'store URL',
        ]);
    }
}
