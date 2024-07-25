<?php

use function Livewire\Volt\{state};
use Livewire\Volt\Component;
use App\Models\DeliveryMethod;
use Illuminate\Database\Eloquent\Collection;

new class  extends Component
{
    public DeliveryMethod $deliveryMethod;

    public array $template_settings_value = [];

    public String $name, $amount = "";

    public function mount()
    {
        $this->template_settings_value = $this->deliveryMethod->template_settings_value;
    }


    public function deleteDwi($name)
    {
        $this->template_settings_value = collect($this->template_settings_value)->filter(function($dwi) use ($name){
            return $dwi['name'] != $name;
        })->values()->toArray();
        $this->deliveryMethod->template_settings_value = $this->template_settings_value;
        $this->deliveryMethod->save();
        $this->deliveryMethod->fresh();
        $this->template_settings_value =  array_reverse($this->deliveryMethod->template_settings_value);
        $this->name = "";
        $this->amount = "";
        $this->alert("Item has been Deleted Successfully!..");
    }


    public function saveSettings()
    {
        $this->validate(['name' => 'required', "amount" => 'required']);
        $template_settings_value = $this->template_settings_value;
        $template_settings_value[] = [
            'name' => $this->name,
            'amount' => $this->amount
        ];
        $this->deliveryMethod->template_settings_value = $template_settings_value;
        $this->deliveryMethod->save();
        $this->alert("Item has been saved Successfully!..");
        $this->template_settings_value =  $this->deliveryMethod->template_settings_value;
        $this->name = "";
        $this->amount = "";
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
                        <h5>List {{ $this->deliveryMethod->name }}</h5>
                        <br/>
                        <div class="table-responsive scrollbar ms-n1 ps-1">
                            <table class="table table-bordered table-sm fs-9 mb-0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($template_settings_value as $value)
                                        <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                            <td class="align-middle text-wrap">{{ $loop->iteration }}</td>
                                            <td class="align-middle text-wrap">{{ $value['name'] }}</td>
                                            <td class="align-middle text-wrap">{{ number_format($value['amount']) }}</td>
                                            <td class="align-middle text-wrap">
                                                <a wire:click="deleteDwi('{{ $value['name'] }}')" class="btn btn-sm btn-danger">
                                                    <span wire:loading wire:target="deleteDwi('{{ $value['name'] }}')" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                                    <i wire:loading.remove wire:target="deleteDwi('{{ $value['name'] }}')" class="fa fa-trash"></i>
                                                    Delete
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <h5>New {{ $this->deliveryMethod->name }}</h5>
                        <form wire:submit.prevent="saveSettings()">
                            <br/>
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" wire:model="name" class="form-control" placeholder="Name">
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Amount</label>
                                <input type="text" wire:model="amount" class="form-control" placeholder="Amount">
                                @error('amount') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <button type="submit" class="btn btn-phoenix-success">
                                <span wire:loading wire:target="saveSettings" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                <i wire:loading.remove wire:target="saveSettings" class="fa fa-save"></i>
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
