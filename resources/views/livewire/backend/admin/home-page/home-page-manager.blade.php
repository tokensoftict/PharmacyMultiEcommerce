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
                                    <td>{{ $item->component_name }}</td>
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
                    <form wire:submit.prevent="store">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Component Name</label>
                                <select wire:model.live="component_name" class="form-select">
                                    <option value="">Select Component</option>
                                    <option value="Horizontal_List">Horizontal_List</option>
                                    <option value="ImageSlider">ImageSlider</option>
                                    <option value="topBrands">topBrands</option>
                                    <option value="FlashDeals">FlashDeals</option>
                                </select>
                                @error('component_name') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            @if(count($availableTypes) > 0)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Type</label>
                                    <select wire:model.live="type" class="form-select">
                                        <option value="">Select Type</option>
                                        @foreach($availableTypes as $val => $text)
                                            <option value="{{ $val }}">{{ $text }}</option>
                                        @endforeach
                                    </select>
                                    @error('type') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            @if(in_array($type, ['classifications', 'manufacturers', 'productcategories', 'ImageSlider', 'lowestClassifications']))
                                <div class="col-md-6 mb-3">
                                    @php
                                        $isMultiple = in_array($component_name, ['topBrands', 'FlashDeals']);
                                        $singularType = $type === 'productcategories' ? 'category' : str_replace('s', '', $type);
                                        if ($type === 'ImageSlider') $singularType = 'Slider';
                                        if ($type === 'lowestClassifications') $singularType = 'Classification';
                                    @endphp
                                    <label class="form-label">Select {{ ucfirst($singularType) }} {{ $isMultiple ? '(Multiple)' : '' }}</label>
                                    <select wire:model="component_id" class="form-select" {{ $isMultiple ? 'multiple' : '' }}>
                                        <option value="">Choose...</option>
                                        @foreach($itemsForSelect as $dataItem)
                                            <option value="{{ $dataItem['id'] }}">{{ $dataItem['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('component_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
@else
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Component ID (Optional)</label>
                                    <input type="text" wire:model="component_id" class="form-control" placeholder="e.g. ID for custom logic">
                                    @error('component_id') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Label (Title shown on app)</label>
                                <input type="text" wire:model="label" class="form-control">
                                @error('label') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Limit</label>
                                <input type="number" wire:model="limit" class="form-control">
                                @error('limit') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Sort Order</label>
                                <input type="number" wire:model="sort_order" class="form-control">
                                @error('sort_order') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Status</label>
                                <select wire:model="status" class="form-select">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                @error('status') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">See All Link</label>
                                <input type="text" wire:model="see_all_link" class="form-control" placeholder="e.g. stock/38/by_classification">
                                @error('see_all_link') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal()">Close</button>
                            <button type="submit" class="btn btn-primary">{{ $editingId ? 'Update' : 'Save' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
