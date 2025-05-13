@section('pageHeaderTitle')
   System Settings
@endsection

@push('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix.'admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">System Settings</li>
@endpush

<div>
    <div class="card shadow-none border my-4">
        <div class="card-body p-0">
            <div class="p-4">
                <div class="row tx-14">

                    <div class="col-sm-6 col-12 offset-sm-3">
                        <div class="mb-3">
                            <label class="form-label">Business Name</label>
                            <input type="text" wire:model="store.name"  class="form-control" name="name" placeholder="Business Name">
                            @error('store.name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">VAT</label>
                            <input type="text" wire:model="store.tax"  class="form-control" name="tax" placeholder="VAT">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Arrival Push Count Trigger</label>
                            <input type="text" wire:model="store.new_arrival_count_trigger"  class="form-control" name="new_arrival_count_trigger" placeholder="">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Store Address Line</label>
                            <textarea name="first_address" wire:model="store.first_address" required="" class="form-control" placeholder="Store Address"></textarea>
                            @error('store.first_address') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Store Address Line 2</label>
                            <textarea name="second_address" wire:model="store.second_address" class="form-control" placeholder="Store Address Line 2"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Store Contact Numbers</label>
                            <textarea name="contact_number" wire:model="store.contact_number" required="" class="form-control" placeholder="Store Contact Numbers"></textarea>
                            @error('store.contact_number') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Delivery Tariff</label>
                            <input type="text" wire:model="store.delivery_tariff"  class="form-control" name="delivery_tariff" placeholder="">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Store Logo</label>
                            <input type="file" name="logo" class="form-control">
                        </div>
                        <img src="{{(isset($this->store['logo']) && $this->store['logo'] !== NULL) ? (is_string($this->store['logo']) ? asset('logo/'.$this->store['logo']) : $this->store['logo']->temporaryUrl()) : asset('logo/placholder.jpg') }}"  class="img-responsive" style="width:30%; margin: auto; display: block;">

                        <div class="mb-3">
                            <label class="form-label">Footer Receipt Notes</label>
                            <textarea name="footer_notes" wire:model="store.footer_notes" class="form-control" placeholder="Footer Receipt Notes"></textarea>
                        </div>

                        <div class="mb-4 mt-4">
                        <h4 class="mb-3">MED REMINDER DISCOUNT SETTINGS</h4>

                            <div class="mb-3">
                                <label class="form-label">Minimum Dosage trigger</label>
                                <input type="text" wire:model="store.dosage_trigger"  class="form-control" name="dosage_trigger" placeholder="">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Discount Percentage</label>
                                <input type="text" wire:model="store.discount_percentage"  class="form-control" name="discount_percentage" placeholder="">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Discount Validity</label>
                                <input type="text" wire:model="store.validity"  class="form-control" name="delivery_tariff" placeholder="Discount Valid for how many days">
                            </div>
                        </div>

                        @if(userCanView('backend.admin.settings.system_settings.update'))
                            <div align="center">
                                <button type="button" wire:click="update" class="btn btn-lg btn-phoenix-primary btn-block">
                                    <i wire:loading.remove wire:target="update"  class="fa fa-save"></i>
                                    <span wire:loading wire:target="update" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    Save Changes
                                </button>
                            </div>
                        @endif
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
