<?php

use function Livewire\Volt\{state};
use Livewire\Volt\Component;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Collection;

new class  extends Component
{
    public PaymentMethod $paymentMethod;

    public string $address = "";

    public function mount()
    {
        $this->address = $this->paymentMethod->template_settings_value['address'];
    }

    public function saveAddress()
    {
        $this->paymentMethod->template_settings_value =  ['address' => $this->address];
        $this->paymentMethod->save();
        $this->alert("Pick Up Store Address has been saved");
    }

}

?>

@section('pageHeaderTitle')
    {{ $this->paymentMethod->name }} Settings
@endsection

@push('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix.'admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix.'backend.admin.settings.payment_methods') }}">Payment Methods</a></li>
    <li class="breadcrumb-item active">{{ $this->paymentMethod->name }} Settings</li>
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
