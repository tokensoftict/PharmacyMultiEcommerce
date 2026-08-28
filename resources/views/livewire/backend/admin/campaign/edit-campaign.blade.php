<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit Campaign: {{ $campaign->name }}</h1>
            <p class="text-muted small mb-0">Update settings, creative content, targeting rules, or CTA action.</p>
        </div>
        <div>
            <a href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix .'backend.admin.campaign.analytics', $campaign->id) }}" class="btn btn-outline-info btn-sm me-2">
                <i class="fa fa-bar-chart me-1"></i> Analytics
            </a>
            <a href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix .'backend.admin.campaign.list') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-arrow-left me-1"></i> Back to Campaigns
            </a>
        </div>
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
                            <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea class="form-control" wire:model="description" rows="2"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <x-select-menu :options="$statusOptions" wire:model="status" id="campaign_status" placeholder="Select Status" />
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Priority (0 = Low, 100 = High)</label>
                                <input type="number" class="form-control" wire:model="priority" min="0" max="100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Store Audience</label>
                                <x-select-menu :options="$storeTypeOptions" wire:model="store_type" id="campaign_store_type" placeholder="Select Store Audience" />
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
                                <x-select-menu :options="$triggerEvents" wire:model="trigger_event" id="campaign_trigger_event" placeholder="Select Trigger Event" />
                                <small class="text-muted">When in the user journey this campaign should be evaluated.</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Frequency Rule</label>
                                <x-select-menu :options="$frequencyRuleOptions" wire:model="frequency_rule" id="campaign_frequency_rule" placeholder="Select Frequency Rule" />
                                <small class="text-muted">Controls how often eligible users can see this popup.</small>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <label class="form-label fw-bold mb-0">Eligibility Conditions (Optional)</label>
                                <div class="text-muted small">Target users based on cart values, order history, or account criteria.</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="addConditionRule">
                                <i class="fa fa-plus me-1"></i> Add Condition Rule
                            </button>
                        </div>

                        @if(count($condition_rules) > 0)
                            <div class="bg-light p-3 rounded-3 mb-3 border">
                                <div class="d-flex align-items-center mb-3">
                                    <span class="me-2 fw-bold text-secondary small">Match Condition:</span>
                                    <select class="form-select form-select-sm w-auto" wire:model="condition_match_type">
                                        <option value="AND">Match ALL Conditions (AND)</option>
                                        <option value="OR">Match ANY Condition (OR)</option>
                                    </select>
                                </div>

                                @foreach($condition_rules as $index => $rule)
                                    <div class="row g-2 align-items-center mb-2" wire:key="cond-rule-{{ $index }}">
                                        <div class="col-md-4">
                                            <select class="form-select form-select-sm" wire:model="condition_rules.{{ $index }}.field">
                                                <option value="cart_total">Cart Total (₦)</option>
                                                <option value="cart_item_count">Cart Items Count</option>
                                                <option value="order_count">Total Lifetime Orders</option>
                                                <option value="order_total_lifetime">Lifetime Spend Amount (₦)</option>
                                                <option value="days_since_signup">Days Since Signup</option>
                                                <option value="days_since_last_order">Days Since Last Order</option>
                                                <option value="loyalty_points">Loyalty Points</option>
                                                <option value="store_type">Store Environment</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-select form-select-sm" wire:model="condition_rules.{{ $index }}.operator">
                                                <option value=">=">&gt;= (Greater or Equal)</option>
                                                <option value=">">&gt; (Greater Than)</option>
                                                <option value="<=">&lt;= (Less or Equal)</option>
                                                <option value="<">&lt; (Less Than)</option>
                                                <option value="==">== (Equals)</option>
                                                <option value="!=">!= (Not Equal)</option>
                                                <option value="contains">Contains text</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control form-control-sm" placeholder="Value (e.g. 5000)" wire:model="condition_rules.{{ $index }}.value">
                                        </div>
                                        <div class="col-md-1 text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Remove Rule" wire:click="removeConditionRule({{ $index }})">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-light border text-muted small py-2 mb-0">
                                <i class="fa fa-info-circle me-1 text-info"></i> No condition rules added. This campaign will trigger for all targeted audience members upon the trigger event.
                            </div>
                        @endif
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
                                <input type="text" class="form-control" wire:model="cta_text">
                            </div>
                            <div class="col-md-6 mb-3" x-data="{ localPreview: null }">
                                <label class="form-label fw-bold">Campaign Image (Optional)</label>
                                
                                <template x-if="localPreview">
                                    <div class="mb-2 p-2 bg-light border rounded text-center position-relative">
                                        <img :src="localPreview" alt="New Image Preview" class="img-thumbnail" style="max-height: 120px;">
                                        <small class="text-success d-block fw-bold mt-1"><i class="fa fa-check"></i> New image selected (will be saved upon update)</small>
                                    </div>
                                </template>

                                <template x-if="!localPreview">
                                    <div>
                                        @if($existing_image)
                                            <div class="mb-2 p-2 bg-light border rounded text-center position-relative">
                                                <img src="{{ asset('storage/' . $existing_image) }}" alt="Current Image" class="img-thumbnail" style="max-height: 120px;">
                                                <small class="text-muted d-block mt-1">Current active image</small>
                                                <button type="button" class="btn btn-sm btn-outline-danger mt-1" wire:click="removeExistingImage">
                                                    <i class="fa fa-trash me-1"></i> Remove Image
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </template>

                                <input type="file" 
                                       class="form-control @error('image_upload') is-invalid @enderror" 
                                       wire:model="image_upload" 
                                       accept="image/*"
                                       @change="localPreview = $event.target.files.length ? URL.createObjectURL($event.target.files[0]) : null">
                                <div wire:loading wire:target="image_upload" class="text-primary small mt-1">
                                    <i class="fa fa-spinner fa-spin me-1"></i> Uploading to server...
                                </div>
                                @error('image_upload') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
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
                            <x-select-menu :options="$deliveryChannelOptions" wire:model="delivery_channel" id="campaign_delivery_channel" placeholder="Select Delivery Channel" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">In-App Display Type</label>
                            <x-select-menu :options="$displayTypeOptions" wire:model="display_type" id="campaign_display_type" placeholder="Select Display Type" />
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
                        <h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-link me-2"></i>CTA Button Action</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Action Type</label>
                            <x-select-menu :options="$actionTypes" wire:model.live="action_type" id="campaign_action_type" placeholder="Select Action Type" />
                        </div>

                        {{-- Dynamic Action Configuration Fields --}}
                        @if($action_type === 'open_product')
                            <div class="mb-3 p-3 bg-light rounded-3 border">
                                <label class="form-label fw-bold small">Search and Select Product</label>
                                <x-utilities.general.stock-search-component 
                                    :value="$action_product_id" 
                                    placeholder="Search for product by name..." 
                                    :classname="''" 
                                    wireModel="action_product_id" 
                                    id="campaign_action_product_id" 
                                />
                                <small class="text-muted mt-1 d-block">Tapping the button will take the user directly to this product's detail page.</small>
                            </div>
                        @elseif($action_type === 'open_category')
                            <div class="mb-3 p-3 bg-light rounded-3 border">
                                <label class="form-label fw-bold small">Select Category</label>
                                <x-select-menu :options="$categories" wire:model="action_category_id" id="campaign_action_category" placeholder="Select Category" />
                                <small class="text-muted mt-1 d-block">Tapping the button will open the product catalog filtered to this category.</small>
                            </div>
                        @elseif(in_array($action_type, ['open_url', 'open_deep_link']))
                            <div class="mb-3 p-3 bg-light rounded-3 border">
                                <label class="form-label fw-bold small">Web Link / URL</label>
                                <input type="url" class="form-control form-control-sm" placeholder="https://generaldrugcentre.com/..." wire:model="action_url">
                                <small class="text-muted mt-1 d-block">Will open this link in the user's default browser or in-app browser.</small>
                            </div>
                        @elseif($action_type === 'apply_coupon')
                            <div class="mb-3 p-3 bg-light rounded-3 border">
                                <label class="form-label fw-bold small">Select or Enter Coupon Code</label>
                                <x-select-menu :options="$coupons" wire:model="action_coupon_code" id="campaign_action_coupon" placeholder="Select Coupon" />
                                <input type="text" class="form-control form-control-sm mt-2" placeholder="Or enter custom coupon code (e.g. SAVE20)" wire:model="action_coupon_code">
                                <small class="text-muted mt-1 d-block">Will navigate user to checkout with this discount code ready.</small>
                            </div>
                        @elseif($action_type === 'open_order')
                            <div class="mb-3 p-3 bg-light rounded-3 border">
                                <label class="form-label fw-bold small">Order ID</label>
                                <input type="number" class="form-control form-control-sm" placeholder="Enter Order ID (e.g. 1042)" wire:model="action_order_id">
                                <small class="text-muted">Navigates user to view order details for this specific order.</small>
                            </div>
                        @elseif($action_type === 'open_cart')
                            <div class="alert alert-info py-2 px-3 small mb-0">
                                <i class="fa fa-shopping-cart me-1"></i> Will navigate user directly to their active shopping cart.
                            </div>
                        @elseif($action_type === 'open_checkout')
                            <div class="alert alert-info py-2 px-3 small mb-0">
                                <i class="fa fa-credit-card me-1"></i> Will navigate user directly to the checkout screen.
                            </div>
                        @elseif($action_type === 'open_store')
                            <div class="alert alert-info py-2 px-3 small mb-0">
                                <i class="fa fa-building me-1"></i> Will navigate user to the main store selector screen.
                            </div>
                        @elseif($action_type === 'none')
                            <div class="alert alert-secondary py-2 px-3 small mb-0">
                                <i class="fa fa-times-circle me-1"></i> Dismiss only — clicking the button closes the campaign popup.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Save Action -->
                <div class="card shadow-sm border-0 rounded-3 bg-light">
                    <div class="card-body text-center py-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-2">
                            <i class="fa fa-check-circle me-2"></i> Update Campaign
                        </button>
                        <a href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix .'backend.admin.campaign.list') }}" class="btn btn-link text-muted btn-sm">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
