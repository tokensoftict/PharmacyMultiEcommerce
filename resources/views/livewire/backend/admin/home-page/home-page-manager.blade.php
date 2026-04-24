<div>
<style>
    .mobile-frame {
        border: 8px solid #333;
        border-radius: 25px;
        height: 600px;
        overflow-y: auto;
        background: #f8f9fa;
        position: relative;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }
    .mobile-header {
        background: #fff;
        padding: 10px;
        text-align: center;
        font-weight: bold;
        border-bottom: 1px solid #ddd;
        font-size: 12px;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .preview-section {
        background: #fff;
        margin-bottom: 10px;
        padding: 10px;
    }
    .preview-title {
        font-size: 14px;
        font-weight: bold;
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
    }
    .preview-see-all {
        font-size: 10px;
        color: #007bff;
    }
    .preview-horizontal-scroll {
        display: flex;
        overflow-x: auto;
        gap: 10px;
        padding-bottom: 5px;
    }
    .preview-product-card {
        min-width: 100px;
        background: #fff;
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 5px;
        text-align: center;
    }
    .preview-product-image {
        height: 60px;
        background: #f0f0f0;
        border-radius: 4px;
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }
    .preview-product-name {
        font-size: 10px;
        height: 24px;
        overflow: hidden;
    }
    .preview-brand-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }
    .preview-brand-item {
        text-align: center;
    }
    .preview-brand-circle {
        width: 45px;
        height: 45px;
        background: #f0f0f0;
        border-radius: 50%;
        margin: 0 auto 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        border: 1px solid #eee;
    }
    .preview-banner {
        height: 120px;
        background: linear-gradient(45deg, #007bff, #00d2ff);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-align: center;
        padding: 20px;
        font-weight: bold;
    }
    .preview-flash-deal {
        background: #fff5f5;
        border: 1px solid #ffcfcf;
        border-radius: 10px;
        padding: 10px;
    }
    .preview-empty {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
        font-style: italic;
        text-align: center;
        padding: 40px;
    }
</style>

<div class="row">
    <!-- Component List Section -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Home Page Component Manager ({{ $storeName }})</h4>
                <div>
                    <button wire:click="openModal" class="btn btn-primary btn-sm">Add New Component</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Component</th>
                                <th>Type</th>
                                <th>Label</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($components as $item)
                                <tr wire:key="home-comp-{{ $item->id }}">
                                    <td>{{ $item->sort_order }}</td>
                                    <td>{{ $componentDisplayNames[$item->component_name] ?? $item->component_name }}</td>
                                    <td>{{ $item->type }}</td>
                                    <td>{{ $item->label }}</td>
                                    <td>
                                        <span class="badge {{ $item->status ? 'bg-success' : 'bg-danger' }}">
                                            {{ $item->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button wire:click="edit({{ $item->id }})" class="btn btn-info btn-sm">Edit</button>
                                        <button wire:click="delete({{ $item->id }})" 
                                                wire:confirm="Are you sure you want to delete this component?"
                                                class="btn btn-danger btn-sm">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $components->links() }}
            </div>
        </div>
    </div>

    <!-- Full Homepage Preview Section -->
    <div class="col-md-4">
        <div class="card sticky-top" style="top: 20px; z-index: 5;">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fa fa-mobile"></i> Full Homepage Preview</h5>
            </div>
            <div class="card-body p-2">
                <div class="mobile-frame" style="height: 700px;">
                    <div class="mobile-header">MediMart Homepage</div>
                    
                    <div class="mobile-content">
                        @foreach($components as $item)
                            @if($item->status)
                                @php $previewItems = $this->getComponentPreviewItems($item); @endphp
                                
                                @if($item->component_name === 'ImageSlider' || $item->component_name === 'PromoCarousel')
                                    <div class="preview-section">
                                        <div class="preview-banner">
                                            <div>
                                                <i class="fa fa-image fa-2x mb-2"></i><br>
                                                {{ $item->label ?: 'Promotion Banner' }}
                                            </div>
                                        </div>
                                    </div>
                                @elseif($item->component_name === 'topBrands')
                                    <div class="preview-section">
                                        <div class="preview-title">
                                            <span>{{ $item->label ?: 'Featured Brands' }}</span>
                                            <span class="preview-see-all">See All ></span>
                                        </div>
                                        <div class="preview-brand-grid">
                                            @foreach($previewItems as $pItem)
                                                <div class="preview-brand-item">
                                                    <div class="preview-brand-circle">
                                                        {{ substr($pItem['name'], 0, 1) }}
                                                    </div>
                                                    <div class="preview-product-name" style="font-size: 8px;">{{ $pItem['name'] }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @elseif($item->component_name === 'Horizontal_List' || $item->component_name === 'FlashDeals')
                                    <div class="preview-section {{ $item->component_name === 'FlashDeals' ? 'preview-flash-deal' : '' }}">
                                        <div class="preview-title">
                                            <span>{{ $item->label ?: 'Product Section' }}</span>
                                            <span class="preview-see-all">See All ></span>
                                        </div>
                                        <div class="preview-horizontal-scroll">
                                            @foreach($previewItems as $pItem)
                                                <div class="preview-product-card">
                                                    <div class="preview-product-image">
                                                        <i class="fa fa-capsules"></i>
                                                    </div>
                                                    <div class="preview-product-name">{{ $pItem['name'] }}</div>
                                                    <div class="mt-1 fw-bold text-success" style="font-size: 10px;">₦5,000</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endforeach
                        
                        @if($components->count() == 0)
                            <div class="preview-empty">
                                No active components to preview for this app.
                            </div>
                        @endif
                        
                        <div class="p-4 text-center text-muted" style="font-size: 10px;">
                            <i class="fa fa-info-circle"></i> Showing active components only
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    @if($isModalOpen)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5); z-index: 1050;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">{{ $editingId ? 'Edit' : 'Add' }} Component</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModal()"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <!-- Form Section -->
                        <div class="col-md-7 p-4">
                            <form wire:submit.prevent="store">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold text-dark">Component Type</label>
                                        <select wire:model.live="component_name" class="form-select border-primary-subtle">
                                            <option value="">Select Component</option>
                                            @foreach($componentDisplayNames as $key => $name)
                                                <option value="{{ $key }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('component_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>

                                    @if(count($availableTypes) > 0)
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label fw-bold text-dark">Data Source (Type)</label>
                                            <select wire:model.live="type" class="form-select border-primary-subtle">
                                                <option value="">Select Type</option>
                                                @foreach($availableTypes as $val => $text)
                                                    <option value="{{ $val }}">{{ $text }}</option>
                                                @endforeach
                                            </select>
                                            @error('type') <span class="text-danger small">{{ $message }}</span> @enderror
                                        </div>
                                    @endif

                                    @if(in_array($type, ['classifications', 'manufacturers', 'productcategories', 'ImageSlider', 'lowestClassifications', 'mixed']))
                                        <div class="col-md-12 mb-3">
                                            @php
                                                $isMultiple = in_array($component_name, ['topBrands', 'FlashDeals', 'ImageSlider']);
                                                $singularType = $type === 'productcategories' ? 'category' : str_replace('s', '', $type);
                                                if ($type === 'ImageSlider') $singularType = 'Slider';
                                                if ($type === 'lowestClassifications') $singularType = 'Classification';
                                                if ($type === 'mixed') $singularType = 'Items';
                                            @endphp
                                            <label class="form-label fw-bold text-dark">Select {{ ucfirst($singularType) }} {{ $isMultiple ? '(Multiple)' : '' }}</label>
                                            <select wire:model.live="component_id" class="form-select border-primary-subtle" {{ $isMultiple ? 'multiple' : '' }} style="height: {{ $isMultiple ? '150px' : 'auto' }}">
                                                <option value="">Choose...</option>
                                                @foreach($itemsForSelect as $dataItem)
                                                    <option value="{{ $dataItem['id'] }}">{{ $dataItem['name'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('component_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                        </div>
                                    @endif

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold text-dark">Display Label (Title on App)</label>
                                        <input type="text" wire:model.live="label" class="form-control border-primary-subtle" placeholder="e.g. Featured Products">
                                        @error('label') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="row g-2">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-bold text-dark">Limit</label>
                                            <input type="number" wire:model="limit" class="form-control border-primary-subtle">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-bold text-dark">Sort Order</label>
                                            <input type="number" wire:model="sort_order" class="form-control border-primary-subtle">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-bold text-dark">Status</label>
                                            <select wire:model="status" class="form-select border-primary-subtle">
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 mb-3">
                                        <label class="form-label fw-bold text-dark">"See All" Link (Optional)</label>
                                        <input type="text" wire:model="see_all_link" class="form-control border-primary-subtle" placeholder="e.g. stock/38/by_classification">
                                    </div>
                                </div>
                                <div class="text-end mt-4">
                                    <button type="button" class="btn btn-light border" wire:click="closeModal()">Cancel</button>
                                    <button type="submit" class="btn btn-primary px-4 shadow-sm">{{ $editingId ? 'Update' : 'Save' }} Changes</button>
                                </div>
                            </form>
                        </div>

                        <!-- Modal Preview Section -->
                        <div class="col-md-5 bg-light p-4 border-start">
                            <h6 class="text-center mb-3 text-primary fw-bold"><i class="fa fa-eye"></i> Live Edit Preview</h6>
                            <div class="mobile-frame" style="height: 480px; box-shadow: inset 0 0 10px rgba(0,0,0,0.05);">
                                <div class="mobile-header">App Preview</div>
                                
                                <div class="mobile-content">
                                    @if($component_name)
                                        @php $previewItems = $this->getComponentPreviewItems(); @endphp
                                        
                                        @if($component_name === 'ImageSlider' || $component_name === 'PromoCarousel')
                                            <div class="preview-section">
                                                <div class="preview-banner">
                                                    <div>
                                                        <i class="fa fa-image fa-2x mb-2"></i><br>
                                                        {{ $label ?: 'Promotion Banner' }}
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif($component_name === 'topBrands')
                                            <div class="preview-section">
                                                <div class="preview-title">
                                                    <span>{{ $label ?: 'Featured Brands' }}</span>
                                                    <span class="preview-see-all">See All ></span>
                                                </div>
                                                <div class="preview-brand-grid">
                                                    @foreach($previewItems as $pItem)
                                                        <div class="preview-brand-item">
                                                            <div class="preview-brand-circle">
                                                                {{ substr($pItem['name'], 0, 1) }}
                                                            </div>
                                                            <div class="preview-product-name" style="font-size: 8px;">{{ $pItem['name'] }}</div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @elseif($component_name === 'Horizontal_List' || $component_name === 'FlashDeals')
                                            <div class="preview-section {{ $component_name === 'FlashDeals' ? 'preview-flash-deal' : '' }}">
                                                <div class="preview-title">
                                                    <span>{{ $label ?: 'Product Section' }}</span>
                                                    <span class="preview-see-all">See All ></span>
                                                </div>
                                                <div class="preview-horizontal-scroll">
                                                    @foreach($previewItems as $pItem)
                                                        <div class="preview-product-card">
                                                            <div class="preview-product-image">
                                                                <i class="fa fa-capsules"></i>
                                                            </div>
                                                            <div class="preview-product-name">{{ $pItem['name'] }}</div>
                                                            <div class="mt-1 fw-bold text-success" style="font-size: 10px;">₦5,000</div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <div class="preview-empty">
                                            Select a component type to see the live preview here.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
</div>
