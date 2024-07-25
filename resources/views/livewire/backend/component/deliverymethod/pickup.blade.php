<?php

use function Livewire\Volt\{state};
use Livewire\Volt\Component;
use App\Models\DeliveryMethod;
use Illuminate\Database\Eloquent\Collection;

new class  extends Component
{
    public DeliveryMethod $deliveryMethod;

    public string $address = "";

    public function mount()
    {
        $this->address = $this->deliveryMethod->template_settings_value['address'];
    }

    public function saveAddress()
    {
        $this->deliveryMethod->template_settings_value =  ['address' => $this->address];
        $this->deliveryMethod->save();
        $this->alert("Pick Up Store Address has been saved");
    }

}

?>

@section('pageHeaderTitle')
    {{ $this->deliveryMethod->name }} Settings
@endsection

@push('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('backend.admin.settings.delivery_methods') }}">Delivery Methods</a></li>
    <li class="breadcrumb-item active">{{ $this->deliveryMethod->name }} Settings</li>
@endpush

<div>
    <div class="card shadow-none border my-4">
        <div class="card-body p-0">
            <div class="p-4">
                <div class="row tx-14">
                    <div class="col-md-7">
                        <h5>Pick Up Store Address</h5>
                        <br/>
                        <form wire:submit.prevent="saveAddress()">
                            <div class="mb-3">
                                <label class="form-label">Store Address</label>
                                <textarea class="form-control" wire:model="address" placeholder="Store Address"></textarea>
                                @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <button type="submit" class="btn btn-phoenix-success">
                                <span wire:loading wire:target="saveAddress" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                <i wire:loading.remove wire:target="saveAddress" class="fa fa-save"></i>
                                Save
                            </button>
                            <br/>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

