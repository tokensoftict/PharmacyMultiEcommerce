@section('pageHeaderTitle')
    Seasonal Experience
@endsection

@push('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix.'admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Seasonal Experience</li>
@endpush

<div>
    {{-- ───────────── Master switch ───────────── --}}
    <div class="card shadow-none border mb-4">
        <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h5 class="mb-1"><i class="fa fa-snowflake me-2 text-primary"></i>Master switch</h5>
                <span class="text-muted small">
                    When OFF, every seasonal effect disappears and the app returns to its normal appearance, regardless of campaign schedules.
                </span>
            </div>
            <div class="form-check form-switch fs-4 mb-0">
                <input class="form-check-input" type="checkbox" role="switch" id="seasonalMaster" wire:model.live="masterEnabled">
                <label class="form-check-label fs-6 fw-bold" for="seasonalMaster">{{ $masterEnabled ? 'ON' : 'OFF' }}</label>
            </div>
        </div>
    </div>

    @if(! $showForm)
        {{-- ───────────── Campaign list ───────────── --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Campaigns</h4>
            <button type="button" class="btn btn-primary" wire:click="create"><i class="fa fa-plus me-1"></i>New campaign</button>
        </div>

        <div class="card shadow-none border">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-body-tertiary">
                        <tr>
                            <th>Campaign</th><th>Theme</th><th>Schedule</th><th class="text-center">Priority</th><th>Status</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($campaigns as $c)
                        <tr wire:key="sc-{{ $c->id }}">
                            <td>
                                <div class="fw-bold">{{ $c->campaign_name }}</div>
                                <div class="text-muted small">{{ \Illuminate\Support\Str::limit($c->campaign_description, 60) }}</div>
                            </td>
                            <td><span class="badge bg-info text-dark">{{ str_replace('_', ' ', $c->theme) }}</span></td>
                            <td class="small">
                                {{ $c->startsAt()?->format('d M Y') ?? 'No start' }} → {{ $c->endsAt()?->format('d M Y') ?? 'No end' }}
                                <div class="text-muted">{{ $c->tz() }}</div>
                            </td>
                            <td class="text-center">{{ $c->priority }}</td>
                            <td>
                                @if($activeId === $c->id)
                                    <span class="badge bg-success">Live now</span>
                                @elseif(! $c->enabled)
                                    <span class="badge bg-secondary">Inactive</span>
                                @elseif($c->endsAt() && $c->endsAt()->isPast())
                                    <span class="badge bg-dark">Expired</span>
                                @elseif($c->startsAt() && $c->startsAt()->isFuture())
                                    <span class="badge bg-warning text-dark">Scheduled</span>
                                @else
                                    <span class="badge bg-light text-dark border">Overridden / master off</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <button class="btn btn-sm btn-outline-secondary" wire:click="toggle({{ $c->id }})">
                                    {{ $c->enabled ? 'Deactivate' : 'Activate' }}
                                </button>
                                <button class="btn btn-sm btn-outline-primary" wire:click="edit({{ $c->id }})"><i class="fa fa-edit"></i></button>
                                <button class="btn btn-sm btn-outline-danger"
                                        wire:click="destroy({{ $c->id }})"
                                        wire:confirm="Delete this campaign? This cannot be undone."><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-5">No campaigns yet. Create your first seasonal campaign.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <p class="text-muted small mt-2">
            When campaigns overlap, the one with the highest priority is shown (ties: most recently started).
            Changes reach the app on its next launch / return to foreground.
        </p>
    @else
        {{-- ───────────── Form + preview ───────────── --}}
        @php
            $f = $form;
            $primary = $f['primary_festive_color'] ?: ($f['theme'] === 'new_year' ? '#D4AF37' : '#C62828');
            $secondary = $f['secondary_festive_color'] ?: ($f['theme'] === 'new_year' ? '#1A1A2E' : '#2E7D32');
        @endphp
        <form wire:submit="save">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card shadow-none border mb-4">
                        <div class="card-header bg-body-tertiary"><h5 class="mb-0">{{ $editingId ? 'Edit' : 'New' }} campaign</h5></div>
                        <div class="card-body row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Campaign name</label>
                                <input type="text" class="form-control" wire:model="form.campaign_name" placeholder="Christmas Celebration 2026">
                                @error('form.campaign_name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Theme</label>
                                <select class="form-select" wire:model.live="form.theme">
                                    <option value="christmas">Christmas</option>
                                    <option value="new_year">New Year</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" rows="2" wire:model="form.campaign_description"></textarea>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Start date</label>
                                <input type="date" class="form-control" wire:model="form.start_date">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">End date (inclusive)</label>
                                <input type="date" class="form-control" wire:model="form.end_date">
                                @error('form.end_date') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Timezone</label>
                                <select class="form-select" wire:model="form.timezone">
                                    @foreach($timezones as $tz) <option value="{{ $tz }}">{{ $tz }}</option> @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Priority</label>
                                <input type="number" class="form-control" wire:model="form.priority">
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="scEnabled" wire:model="form.enabled">
                                    <label class="form-check-label fw-bold" for="scEnabled">Campaign active (still follows the schedule)</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-none border mb-4">
                        <div class="card-header bg-body-tertiary"><h5 class="mb-0">Effects</h5></div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach($flags as $group => $items)
                                    <div class="col-md-6">
                                        <div class="fw-bold small text-uppercase text-muted mb-1">{{ $group }}</div>
                                        @foreach($items as $key => $label)
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="sc_{{ $key }}" wire:model.live="form.{{ $key }}">
                                                <label class="form-check-label" for="sc_{{ $key }}">{{ $label }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                            <hr>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Snow intensity</label>
                                    <select class="form-select" wire:model="form.snowfall_intensity">
                                        <option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Snow speed</label>
                                    <select class="form-select" wire:model="form.snowfall_speed">
                                        <option value="slow">Slow</option><option value="normal">Normal</option><option value="fast">Fast</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Snow opacity ({{ $f['snowfall_opacity'] }})</label>
                                    <input type="range" class="form-range" min="0.1" max="1" step="0.05" wire:model.live.debounce.300ms="form.snowfall_opacity">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Decoration intensity</label>
                                    <select class="form-select" wire:model="form.decoration_intensity">
                                        <option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Lights animation</label>
                                    <select class="form-select" wire:model="form.lights_intensity">
                                        <option value="low">Subtle</option><option value="medium">Medium</option><option value="high">Lively</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Fireworks intensity</label>
                                    <select class="form-select" wire:model="form.fireworks_intensity">
                                        <option value="low">Low</option><option value="medium">Medium</option><option value="high">High</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Effects opacity ({{ $f['background_effect_opacity'] }})</label>
                                    <input type="range" class="form-range" min="0.1" max="1" step="0.05" wire:model.live.debounce.300ms="form.background_effect_opacity">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Logo decoration (optional)</label>
                                    <input type="text" class="form-control" wire:model="form.logo_decoration" placeholder="santa_hat">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Primary festive colour</label>
                                    <input type="color" class="form-control form-control-color w-100" value="{{ $primary }}" wire:model.live="form.primary_festive_color">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Secondary festive colour</label>
                                    <input type="color" class="form-control form-control-color w-100" value="{{ $secondary }}" wire:model.live="form.secondary_festive_color">
                                </div>
                            </div>
                            @foreach(['snowfall_opacity','background_effect_opacity','primary_festive_color','secondary_festive_color'] as $k)
                                @error('form.'.$k) <div class="text-danger small">{{ $message }}</div> @enderror
                            @endforeach
                        </div>
                    </div>

                    <div class="card shadow-none border mb-4">
                        <div class="card-header bg-body-tertiary"><h5 class="mb-0">Greeting</h5></div>
                        <div class="card-body row g-3">
                            <div class="col-md-7">
                                <label class="form-label">Message</label>
                                <input type="text" class="form-control" wire:model.live.debounce.400ms="form.greeting_message" placeholder="Merry Christmas!">
                                @error('form.greeting_message') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Subtitle</label>
                                <input type="text" class="form-control" wire:model.live.debounce.400ms="form.greeting_subtitle">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Alignment</label>
                                <select class="form-select" wire:model.live="form.greeting_alignment">
                                    <option value="left">Left</option><option value="center">Center</option><option value="right">Right</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Text style</label>
                                <select class="form-select" wire:model.live="form.greeting_text_style">
                                    <option value="normal">Normal</option><option value="bold">Bold</option>
                                    <option value="italic">Italic</option><option value="script">Elegant / script</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Background (hex)</label>
                                <input type="text" class="form-control" wire:model.live.debounce.400ms="form.greeting_background_color" placeholder="#C62828">
                                @error('form.greeting_background_color') <span class="text-danger small">Use a hex colour, e.g. #C62828</span> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Text colour (hex)</label>
                                <input type="text" class="form-control" wire:model.live.debounce.400ms="form.greeting_text_color" placeholder="#FFFFFF">
                                @error('form.greeting_text_color') <span class="text-danger small">Use a hex colour, e.g. #FFFFFF</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-none border mb-4">
                        <div class="card-header bg-body-tertiary"><h5 class="mb-0">Seasonal promo banner</h5></div>
                        <div class="card-body row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Title</label>
                                <input type="text" class="form-control" wire:model.live.debounce.400ms="form.banner_title">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Subtitle</label>
                                <input type="text" class="form-control" wire:model.live.debounce.400ms="form.banner_subtitle">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Link (https://… or in-app path like /products)</label>
                                <input type="text" class="form-control" wire:model="form.banner_link">
                                @error('form.banner_link') <span class="text-danger small">Enter an https:// URL or an in-app path starting with /</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Banner image (max 5MB)</label>
                                <input type="file" class="form-control" wire:model="bannerUpload" accept="image/*">
                                @error('bannerUpload') <span class="text-danger small">{{ $message }}</span> @enderror
                                @if($existingBanner && ! $bannerUpload)
                                    <div class="mt-2 d-flex align-items-center gap-2">
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($existingBanner) }}" height="48" class="rounded border">
                                        <div class="form-check"><input type="checkbox" class="form-check-input" id="rmBanner" wire:model="removeBanner"><label for="rmBanner" class="form-check-label small">Remove image</label></div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-none border mb-4">
                        <div class="card-header bg-body-tertiary"><h5 class="mb-0">New Year</h5></div>
                        <div class="card-body row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Celebration message</label>
                                <input type="text" class="form-control" wire:model="form.celebration_message" placeholder="Wishing You Success, Happiness and Prosperity!">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Countdown message</label>
                                <input type="text" class="form-control" wire:model="form.countdown_message" placeholder="Happy New Year in">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Countdown target (campaign timezone)</label>
                                <input type="datetime-local" class="form-control" wire:model="form.countdown_target_date">
                                @error('form.countdown_target_date') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mb-4">
                        <button type="submit" class="btn btn-primary">
                            <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1"></span>
                            <i wire:loading.remove wire:target="save" class="fa fa-save me-1"></i>Save campaign
                        </button>
                        <button type="button" class="btn btn-outline-secondary" wire:click="cancel">Cancel</button>
                    </div>
                </div>

                {{-- ───────────── Preview ───────────── --}}
                <div class="col-lg-4">
                    <div class="position-sticky" style="top: 80px;">
                        <div class="card shadow-none border">
                            <div class="card-header bg-body-tertiary"><h5 class="mb-0">Preview</h5></div>
                            <div class="card-body">
                                <div class="mx-auto rounded-4 border overflow-hidden position-relative bg-white" style="max-width: 300px; min-height: 420px;">
                                    {{-- header + logo --}}
                                    <div class="p-3 d-flex align-items-center gap-2 position-relative">
                                        <div class="position-relative" style="width:44px;height:44px;">
                                            <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center fw-bold" style="width:44px;height:44px;">PS</div>
                                            @if($f['show_santa_hat'])
                                                <span class="position-absolute" style="top:-14px; left:-6px; font-size:26px; transform: rotate(-18deg);">🎅</span>
                                            @endif
                                        </div>
                                        <div class="fw-bold">PS GDC</div>
                                        @if($f['show_christmas_lights'])
                                            <div class="position-absolute bottom-0 start-0 end-0 d-flex justify-content-around" style="height:8px;">
                                                @foreach(['#E53935','#43A047','#FDD835','#1E88E5','#D4AF37','#E53935','#43A047','#FDD835','#1E88E5','#D4AF37'] as $bulb)
                                                    <span class="rounded-circle" style="width:6px;height:6px;background:{{ $bulb }};box-shadow:0 0 6px {{ $bulb }};"></span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    {{-- greeting --}}
                                    @if($f['show_greeting'] && ($f['greeting_message'] || $f['greeting_subtitle']))
                                        @php
                                            $bg = preg_match('/^#[0-9a-fA-F]{3,8}$/', $f['greeting_background_color'] ?? '') ? $f['greeting_background_color'] : $primary;
                                            $fg = preg_match('/^#[0-9a-fA-F]{3,8}$/', $f['greeting_text_color'] ?? '') ? $f['greeting_text_color'] : '#ffffff';
                                            $style = $f['greeting_text_style'];
                                        @endphp
                                        <div class="mx-3 my-2 p-3 rounded-3" style="background:{{ $bg }}; color:{{ $fg }}; text-align:{{ $f['greeting_alignment'] }};">
                                            <div style="font-size:18px; font-weight:{{ in_array($style, ['bold','script']) ? 700 : 500 }}; font-style:{{ in_array($style, ['italic','script']) ? 'italic' : 'normal' }}; {{ $style === 'script' ? 'font-family: Georgia, serif;' : '' }}">{{ $f['greeting_message'] }}</div>
                                            @if($f['greeting_subtitle'])<div style="font-size:12px; opacity:.9;">{{ $f['greeting_subtitle'] }}</div>@endif
                                        </div>
                                    @endif
                                    {{-- banner --}}
                                    @if($f['show_seasonal_banner'])
                                        <div class="mx-3 my-2 rounded-3 p-3 text-white" style="background: linear-gradient(135deg, {{ $primary }}, {{ $secondary }}); min-height:90px;">
                                            <div class="fw-bold">{{ $f['banner_title'] ?: 'Seasonal banner' }}</div>
                                            <div class="small" style="opacity:.9">{{ $f['banner_subtitle'] }}</div>
                                            @if($bannerUpload)<img src="{{ $bannerUpload->temporaryUrl() }}" class="img-fluid rounded mt-2">@endif
                                        </div>
                                    @endif
                                    @if($f['show_countdown'])
                                        <div class="mx-3 my-2 p-2 rounded-3 text-center small border" style="border-color:{{ $primary }}!important;">
                                            {{ $f['countdown_message'] ?: 'Happy New Year in' }} <strong>10d 04h 12m 30s</strong>
                                        </div>
                                    @endif
                                    @if($f['show_snowfall'])
                                        <div class="position-absolute top-0 start-0 end-0 bottom-0" style="pointer-events:none; opacity:{{ $f['snowfall_opacity'] }};">
                                            @foreach([8,22,35,48,60,72,85,15,55,90] as $i => $left)
                                                <span class="position-absolute text-primary" style="left:{{ $left }}%; top:{{ ($i * 37) % 90 }}%; font-size:{{ 10 + ($i % 3) * 4 }}px;">❄</span>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if($f['show_fireworks'] || $f['show_confetti'] || $f['show_gold_particles'])
                                        <div class="position-absolute bottom-0 start-0 end-0 text-center pb-2" style="pointer-events:none; font-size:22px;">
                                            {{ $f['show_fireworks'] ? '🎆' : '' }}{{ $f['show_confetti'] ? '🎊' : '' }}{{ $f['show_gold_particles'] ? '✨' : '' }}
                                        </div>
                                    @endif
                                </div>
                                <p class="text-muted small mt-3 mb-0">Approximate preview of layout, colours and which effects are on. Animations run in the app. Nothing goes live until you save and the campaign is active in its schedule.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif
</div>

