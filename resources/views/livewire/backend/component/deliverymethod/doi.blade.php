<?php

use function Livewire\Volt\{state};
use Livewire\Volt\Component;
use App\Models\DeliveryMethod;
use Illuminate\Support\Collection;

new class  extends Component
{
    public DeliveryMethod $deliveryMethod;

    public array $template_settings_value = [];

    public String $optionName, $optionAmount, $optionDescription = "";

    public Collection $optionFields;


    public function mount()
    {
        $this->template_settings_value = $this->deliveryMethod->template_settings_value;
        $this->fill(['optionFields' => collect()]);
    }

    public function toggleDwi($name)
    {
        $this->template_settings_value = collect($this->template_settings_value)->map(function($doi) use ($name){
            if($doi['name'] == $name) {
                if (isset($doi['delete_status'])) {
                    unset($doi['delete_status']);
                } else {
                    $doi['delete_status'] = true;
                }
            }
            return $doi;

        })->values()->toArray();

        $this->deliveryMethod->template_settings_value = $this->template_settings_value;
        $this->deliveryMethod->save();
        $this->deliveryMethod->fresh();
        $this->template_settings_value =  $this->deliveryMethod->template_settings_value;

        $this->name = $this->optionDescription = $this->optionAmount = "";
        $this->optionFields = collect();
        $this->alert("Item has disable Successfully!..");
    }

    public function saveSettings()
    {
        $this->validate(['optionName' => 'required', "optionAmount" => 'required|numeric']);

        $template_settings_value = $this->template_settings_value;
        $template_settings_value[] = [
            'name' => $this->optionName,
            'amount' => $this->optionAmount,
            'description' => $this->optionDescription,
            'option' => $this->optionFields->values()->toArray()
        ];
        $this->deliveryMethod->template_settings_value = $template_settings_value;
        $this->deliveryMethod->save();
        $this->template_settings_value =  $this->deliveryMethod->template_settings_value;
        $this->optionName = $this->optionDescription = $this->optionAmount = "";
        $this->optionFields = collect();
        $this->alert("Item has been saved Successfully!..");
    }

    public function addFieldType()
    {
        $this->optionFields->push(
            [
                "name" => "",
                "type" => "textbox"
            ]
        );
    }


    public function deleteFieldType($index)
    {
       $this->optionFields->pull($index);
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
                            <table class="table  table-sm fs-9 mb-0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Option Name</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Option</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($template_settings_value as $value)

                                    <tr>
                                        <td class="align-middle text-wrap">{{ $loop->iteration }}</td>
                                        <td class="align-middle text-wrap">{{ $value['name'] }}</td>
                                        <td class="align-middle text-wrap">{{ number_format($value['amount']) }}</td>
                                        <td class="align-middle text-wrap">
                                            {!!   label((!isset($value['delete_status']) ? 'Active' : 'Inactive'), (!isset($value['delete_status']) ? 'success' : 'danger')) !!}
                                        </td>
                                        <td class="align-middle text-wrap">
                                            @foreach($value['option'] as $option)
                                                {{ $option['name']."," }}
                                            @endforeach
                                        </td>
                                        <td class="align-middle text-wrap">
                                            @if(!isset($value['delete_status']))
                                                <a wire:click="toggleDwi('{{ $value['name'] }}')" class="btn btn-sm btn-phoenix-danger">
                                                    <span wire:loading wire:target="toggleDwi('{{ $value['name'] }}')" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                                    <i wire:loading.remove wire:target="toggleDwi('{{ $value['name'] }}')" class="fa fa-trash"></i>
                                                    Disable
                                                </a>
                                            @else
                                                <a wire:click="toggleDwi('{{ $value['name'] }}')" class="btn btn-sm btn-phoenix-success">
                                                    <span wire:loading wire:target="toggleDwi('{{ $value['name'] }}')" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                                    <i wire:loading.remove wire:target="toggleDwi('{{ $value['name'] }}')" class="fa fa-check"></i>
                                                    Enable
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <h5>Add {{ $this->deliveryMethod->name }}</h5>
                        <br/>
                        <form wire:submit.prevent="saveSettings()">
                            <br/>
                            <div class="mb-3">
                                <label class="form-label">Option Name</label>
                                <input type="text" wire:model="optionName" class="form-control" placeholder="Name">
                                @error('optionName') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Amount</label>
                                <input type="text" wire:model="optionAmount" class="form-control" placeholder="Amount">
                                @error('optionAmount') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" wire:model="optionDescription" placeholder="Description"></textarea>
                            </div>
                            <div class="table-responsive scrollbar ms-n1 ps-1">
                                <table class="table  table-sm fs-9 mb-0">
                                    <thead>
                                    <tr>
                                        <th>Field Name</th>
                                        <th>Field Type</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($this->optionFields as $key => $optionField)
                                            <tr>
                                                <td><input type="text" class="form-control form-control-sm" :key="tests_{{ $key }}_title" wire:model="optionFields.{{$key}}.name"></td>
                                                <td>{{ ucwords($optionFields[$key]['type']) }}</td>
                                                <td class="text-end">
                                                    <button type="button" wire:click="deleteFieldType('{{ $key }}')" class="btn btn-sm btn-phoenix-danger">
                                                        <span wire:loading wire:target="deleteFieldType('{{ $key }}')" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                                        <i wire:loading.remove wire:target="deleteFieldType('{{ $key }}')" class="fa fa-trash"></i>
                                                        Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th class="text-end">
                                            <button type="button"  wire:click="addFieldType()" class="btn btn-sm btn-success">
                                                <span wire:loading wire:target="addFieldType" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                                <i wire:loading.remove wire:target="addFieldType" class="fa fa-plus"></i>
                                                Add
                                            </button>
                                        </th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <br/>
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
