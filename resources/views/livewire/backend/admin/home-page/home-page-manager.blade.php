<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Home Page Component Manager</h4>
                <div>
                    <select wire:model.live="selectedApp" class="form-select form-select-sm d-inline-block w-auto me-2">
                        @foreach($apps as $app)
                            <option value="{{ $app->id }}">{{ $app->name }}</option>
                        @endforeach
                    </select>
                    <button wire:click="openModal()" class="btn btn-primary btn-sm">Add New Component</button>
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
                                <tr>
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
                                        <button wire:click="delete({{ $item->id }})" class="btn btn-danger btn-sm" onclick="confirm('Are you sure?') || event.stopImmediatePropagation()">Delete</button>
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

    <!-- Modal -->
    @if($isModalOpen)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $editingId ? 'Edit' : 'Add' }} Component</h5>
                    <button type="button" class="btn-close" wire:click="closeModal()"></button>
                </div>
                <div class="modal-body">
                    <style>
                        .mobile-frame {
                            border: 8px solid #333;
                            border-radius: 25px;
                            height: 500px;
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
                            width: 50px;
                            height: 50px;
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
                        <!-- Form Section -->
                        <div class="col-md-7 border-end">
                            <form wire:submit.prevent="store">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">Component Type</label>
                                        <select wire:model.live="component_name" class="form-select">
                                            <option value="">Select Component</option>
                                            @foreach($componentDisplayNames as $key => $name)
                                                <option value="{{ $key }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('component_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>

                                    @if(count($availableTypes) > 0)
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label fw-bold">Data Source (Type)</label>
                                            <select wire:model.live="type" class="form-select">
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
                                            <label class="form-label fw-bold">Select {{ ucfirst($singularType) }} {{ $isMultiple ? '(Multiple)' : '' }}</label>
                                            <select wire:model.live="component_id" class="form-select" {{ $isMultiple ? 'multiple' : '' }} style="height: {{ $isMultiple ? '150px' : 'auto' }}">
                                                <option value="">Choose...</option>
                                                @foreach($itemsForSelect as $dataItem)
                                                    <option value="{{ $dataItem['id'] }}">{{ $dataItem['name'] }}</option>
                                                @endforeach
                                            </select>
                                            @error('component_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                        </div>
                                    @endif

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">Display Label (Title on App)</label>
                                        <input type="text" wire:model.live="label" class="form-control" placeholder="e.g. Featured Products">
                                        @error('label') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Limit</label>
                                        <input type="number" wire:model="limit" class="form-control">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Sort Order</label>
                                        <input type="number" wire:model="sort_order" class="form-control">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Status</label>
                                        <select wire:model="status" class="form-select">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label fw-bold">"See All" Link (Optional)</label>
                                        <input type="text" wire:model="see_all_link" class="form-control" placeholder="e.g. stock/38/by_classification">
                                    </div>
                                </div>
                                <div class="text-end mt-3">
                                    <button type="button" class="btn btn-secondary" wire:click="closeModal()">Close</button>
                                    <button type="submit" class="btn btn-primary px-4">{{ $editingId ? 'Update Component' : 'Save Component' }}</button>
                                </div>
                            </form>
                        </div>

                        <!-- Preview Section -->
                        <div class="col-md-5 bg-light p-3">
                            <h6 class="text-center mb-3 text-muted"><i class="fa fa-mobile-phone"></i> Live Mobile Preview</h6>
                            <div class="mobile-frame">
                                <div class="mobile-header">MediMart App</div>
                                
                                <div class="mobile-content">
                                    @if($component_name)
                                        @php $previewItems = $this->getPreviewItems(); @php
                                        
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
                                                            <div class="preview-product-name">{{ $pItem['name'] }}</div>
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
                                        
                                        <div class="p-3 text-center text-muted" style="font-size: 10px;">
                                            Other content below...
                                        </div>
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
