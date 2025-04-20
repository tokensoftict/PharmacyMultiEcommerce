<?php

use function Livewire\Volt\{state};
use Livewire\Volt\Component;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Collection;

new class  extends Component {

    public PaymentMethod $paymentMethod;
    public string $environment;
    public string $sec_key;
    public string $pub_key;

    public array $data = [];

    public function mount()
    {
        $this->environment = $this->paymentMethod->template_settings_value['environment'] ?? "";
        $this->sec_key = $this->paymentMethod->template_settings_value['sec_key'] ?? "";
        $this->pub_key = $this->paymentMethod->template_settings_value['pub_key'] ?? "";

        $this->data['environment']['options'] = [['id' => 'testing', 'text' => 'Testing'],['id'=> 'production', 'text' => 'Production']];
    }

    public function saveSettings()
    {
        $this->validate([
            'environment' => 'required',
            'sec_key' => 'required',
            'pub_key' => 'required',
        ]);


        $this->paymentMethod->template_settings_value = [
            'environment' => $this->environment,
            'sec_key' => $this->sec_key,
            'pub_key'=> $this->pub_key
        ];

        $this->paymentMethod->save();

        $this->alert("Flutterwave Settings has been saved successfully");
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
                        <h5>{{ $this->paymentMethod->name }} Settings</h5>
                        <br/>
                        <form id="validate" wire:submit.prevent="saveSettings()">
                            <div class="mb-3">
                                <label class="form-label">Environment</label>
                                <x-dropdown-select-menu wire:model="environment" id="environment"  :options="$data['environment']['options']"/>
                                @error("environment") <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Secret Key</label>
                                <input type="text" wire:model="sec_key" class="form-control" placeholder="Secret key">
                                @error("sec_key") <span class="text-danger">{{ $message }}</span> @enderror
                            </div>


                            <div class="mb-3">
                                <label class="form-label">Public Key</label>
                                <input type="text" wire:model="pub_key" class="form-control" placeholder="Public key">
                                @error("pub_key") <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <br/>
                            <button type="submit" class="btn btn-phoenix-success">
                                <span wire:loading wire:target="saveSettings" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                <i wire:loading.remove wire:target="saveSettings" class="fa fa-save"></i>
                                Save
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
