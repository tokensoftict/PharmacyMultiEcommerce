@section('pageHeaderTitle')
   App Update Settings
@endsection

@push('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix.'admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">App Update Settings</li>
@endpush

<div>
    <div class="row g-4 my-4">
        <!-- Android Settings -->
        <div class="col-md-6 col-12">
            <div class="card shadow-none border h-100">
                <div class="card-header bg-body-tertiary py-3">
                    <h4 class="mb-0 text-primary">
                        <i class="fab fa-android me-2"></i>Android Update Settings
                    </h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Latest Version Name</label>
                        <input type="text" wire:model="android_version_name" class="form-control" placeholder="e.g., 1.15.0">
                        @error('android_version_name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Latest Version Code</label>
                        <input type="number" wire:model="android_version_code" class="form-control" placeholder="e.g., 115">
                        @error('android_version_code') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" wire:model="android_force_update" id="androidForceUpdate">
                            <label class="form-check-label fw-bold" for="androidForceUpdate">Force Update</label>
                        </div>
                        <span class="text-muted small">If enabled, users running older versions will be blocked from using the app until they update.</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Update Message</label>
                        <textarea wire:model="android_update_message" class="form-control" rows="3" placeholder="A new version of the app is available. Please update to continue."></textarea>
                        @error('android_update_message') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Play Store URL</label>
                        <input type="url" wire:model="android_store_url" class="form-control" placeholder="https://play.google.com/store/apps/details?id=...">
                        @error('android_store_url') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model="android_is_active" id="androidIsActive">
                            <label class="form-check-label" for="androidIsActive">Is Active</label>
                        </div>
                    </div>

                    @if(userCanView('backend.admin.settings.system_settings.update'))
                        <div class="mt-4 text-end">
                            <button type="button" wire:click="updateAndroid" class="btn btn-primary">
                                <i wire:loading.remove wire:target="updateAndroid" class="fa fa-save me-1"></i>
                                <span wire:loading wire:target="updateAndroid" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                Save Android Configuration
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- iOS Settings -->
        <div class="col-md-6 col-12">
            <div class="card shadow-none border h-100">
                <div class="card-header bg-body-tertiary py-3">
                    <h4 class="mb-0 text-info">
                        <i class="fab fa-apple me-2"></i>iOS Update Settings
                    </h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Latest Version Name</label>
                        <input type="text" wire:model="ios_version_name" class="form-control" placeholder="e.g., 1.15.0">
                        @error('ios_version_name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Latest Version Code</label>
                        <input type="number" wire:model="ios_version_code" class="form-control" placeholder="e.g., 115">
                        @error('ios_version_code') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" wire:model="ios_force_update" id="iosForceUpdate">
                            <label class="form-check-label fw-bold" for="iosForceUpdate">Force Update</label>
                        </div>
                        <span class="text-muted small">If enabled, users running older versions will be blocked from using the app until they update.</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Update Message</label>
                        <textarea wire:model="ios_update_message" class="form-control" rows="3" placeholder="A new version of the app is available. Please update to continue."></textarea>
                        @error('ios_update_message') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">App Store URL</label>
                        <input type="url" wire:model="ios_store_url" class="form-control" placeholder="https://apps.apple.com/app/id...">
                        @error('ios_store_url') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model="ios_is_active" id="iosIsActive">
                            <label class="form-check-label" for="iosIsActive">Is Active</label>
                        </div>
                    </div>

                    @if(userCanView('backend.admin.settings.system_settings.update'))
                        <div class="mt-4 text-end">
                            <button type="button" wire:click="updateIos" class="btn btn-info text-white">
                                <i wire:loading.remove wire:target="updateIos" class="fa fa-save me-1"></i>
                                <span wire:loading wire:target="updateIos" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                Save iOS Configuration
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
