<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Create Campaign</h1>
            <p class="text-muted small mb-0">Build high-converting in-app and push campaigns with custom targeting rules.</p>
        </div>
        <a href="{{ route('backend.admin.campaign.list') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa fa-arrow-left me-1"></i> Back to Campaigns
        </a>
    </div>

    <form wire:submit.prevent="save">
        <div class="row g-4">
            <!-- Left Column: Core Setup -->
            <div class="col-lg-8">
                <!-- Card 1: Basic Info -->
                <div class="card shadow-sm mb-4 border-0 rounded-3">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-info-circle me-2"></i>1. Campaign Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Campaign Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="e.g. Black Friday 20% Off Cart Pop-up">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea class="form-control" wire:model="description" rows="2" placeholder="Internal notes or goals for this campaign..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <select class="form-select" wire:model="status">
                                    <option value="draft">Draft</option>
                                    <option value="active">Active</option>
                                    <option value="paused">Paused</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Priority (0 = Low, 100 = High)</label>
                                <input type="number" class="form-control" wire:model="priority" min="0" max="100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Store Audience</label>
                                <select class="form-select" wire:model="store_type">
                                    <option value="both">Both Retail & Wholesale</option>
                                    <option value="retail">Supermarket Retail Only</option>
                                    <option value="wholesale">Wholesale Only</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Trigger & Conditions -->
                <div class="card shadow-sm mb-4 border-0 rounded-3">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-bolt me-2"></i>2. Trigger Event & Eligibility Conditions</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Trigger Event <span class="text-danger">*</span></label>
                                <select class="form-select" wire:model="trigger_event">
                                    @foreach($triggerEvents as $evt)
                                        <option value="{{ $evt }}">{{ $evt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Frequency Rule</label>
                                <select class="form-select" wire:model="frequency_rule">
                                    <option value="once_ever">Once Ever Per User</option>
                                    <option value="once_per_session">Once Per App Session</option>
                                    <option value="once_per_day">Once Per Day</option>
                                    <option value="cooldown">Cooldown Period</option>
                                    <option value="unlimited">Unlimited (Show Every Trigger)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Condition Tree JSON (Optional)</label>
                            <textarea class="form-control font-monospace text-sm" wire:model="conditions_json" rows="4" placeholder='{ "operator": "AND", "rules": [{ "field": "cart_total", "operator": ">=", "value": 5000 }] }'></textarea>
                            <small class="text-muted">Supported fields: <code>cart_total</code>, <code>cart_item_count</code>, <code>order_count</code>, <code>days_since_signup</code>.</small>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Creative Content -->
                <div class="card shadow-sm mb-4 border-0 rounded-3">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-paint-brush me-2"></i>3. Creative & Message Content</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">In-App Title</label>
                            <input type="text" class="form-control" wire:model="title" placeholder="e.g. Special Offer Just For You!">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">In-App Message</label>
                            <textarea class="form-control" wire:model="message" rows="3" placeholder="Get 15% discount on your next order..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Button CTA Text</label>
                                <input type="text" class="form-control" wire:model="cta_text" placeholder="e.g. Claim Discount Now">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Campaign Image (Optional)</label>
                                <input type="file" class="form-control" wire:model="image_upload">
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-bold text-secondary mb-3"><i class="fa fa-bell me-2"></i>Push Notification Override (Optional)</h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Push Title</label>
                            <input type="text" class="form-control" wire:model="push_title" placeholder="Defaults to In-App Title if empty">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Push Body</label>
                            <textarea class="form-control" wire:model="push_body" rows="2" placeholder="Defaults to In-App Message if empty"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Settings & Actions -->
            <div class="col-lg-4">
                <!-- Delivery & Display -->
                <div class="card shadow-sm mb-4 border-0 rounded-3">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-desktop me-2"></i>Delivery & Format</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Delivery Channel</label>
                            <select class="form-select" wire:model="delivery_channel">
                                <option value="both">In-App & Push Notification</option>
                                <option value="in_app">In-App Popup Only</option>
                                <option value="push">Push Notification Only</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">In-App Display Type</label>
                            <select class="form-select" wire:model="display_type">
                                <option value="modal">Center Modal Popup</option>
                                <option value="bottom_sheet">Slide-up Bottom Sheet</option>
                                <option value="fullscreen">Full Screen Overlay</option>
                                <option value="banner">Top Banner</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Max Total Impressions Cap</label>
                            <input type="number" class="form-control" wire:model="max_impressions" placeholder="Leave empty for unlimited">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Max Impressions Per User</label>
                            <input type="number" class="form-control" wire:model="max_impressions_per_user" placeholder="e.g. 3">
                        </div>
                    </div>
                </div>

                <!-- CTA Action -->
                <div class="card shadow-sm mb-4 border-0 rounded-3">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-link me-2"></i>CTA Action Target</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Action Type</label>
                            <select class="form-select" wire:model="action_type">
                                <option value="none">None (Dismiss Only)</option>
                                <option value="open_product">Open Specific Product</option>
                                <option value="open_category">Open Specific Category</option>
                                <option value="open_cart">Open Cart</option>
                                <option value="open_checkout">Open Checkout</option>
                                <option value="open_store">Open Store Home</option>
                                <option value="open_url">Open External Web Link</option>
                                <option value="apply_coupon">Apply Coupon Code</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Action Payload JSON</label>
                            <textarea class="form-control font-monospace text-sm" wire:model="action_data_json" rows="3" placeholder='{ "product_id": 123 } or { "url": "https://..." }'></textarea>
                        </div>
                    </div>
                </div>

                <!-- Save Action -->
                <div class="card shadow-sm border-0 rounded-3 bg-light">
                    <div class="card-body text-center py-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-2">
                            <i class="fa fa-check-circle me-2"></i> Save & Launch Campaign
                        </button>
                        <a href="{{ route('backend.admin.campaign.list') }}" class="btn btn-link text-muted btn-sm">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
