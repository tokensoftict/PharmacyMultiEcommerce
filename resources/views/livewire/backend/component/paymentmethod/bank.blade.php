<?php

use function Livewire\Volt\{state};
use Livewire\Volt\Component;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Collection;
use App\Models\BankAccount;
new class  extends Component
{
    public PaymentMethod $paymentMethod;

    public array $template_settings_value = [];

    public string $bank_id = "";

    public Collection $existingBanks;

    public array $bankAccounts;


    public function mount()
    {
        $bankAccounts = BankAccount::where("status", '1')->get();
        foreach ($bankAccounts as $bankAccount)
        {
            $this->bankAccounts[] = [
                "id" => $bankAccount->id,
                "name" => $bankAccount->account_name." - ".$bankAccount->account_number." (".$bankAccount->bank->name.")",
            ];
        }
        $this->template_settings_value = $this->paymentMethod->template_settings_value;
        $this->existingBanks = BankAccount::query()->with(['bank'])->whereIn('id', $this->template_settings_value)->get();
    }


    public function deleteBank($id)
    {
        $this->template_settings_value = collect($this->template_settings_value)->filter(function($bank_id) use ($id){
            return $bank_id != $id;
        })->values()->toArray();
        $this->paymentMethod->template_settings_value=$this->template_settings_value;
        $this->paymentMethod->save();
        $this->alert("Bank has been Deleted Successfully!..");
        $this->existingBanks = BankAccount::query()->with(['bank'])->whereIn('id', $this->template_settings_value)->get();
        $this->bank_id = "";
    }

    public function saveNewBank()
    {
        $this->validate(['bank_id' => 'required|not_in:'.implode(",", $this->template_settings_value)]);
        $bank_id = $this->bank_id;
        $accounts = BankAccount::find($this->bank_id);
        $template_settings_value = $this->template_settings_value;
        $template_settings_value[] = $bank_id;
        $this->paymentMethod->template_settings_value = $template_settings_value;
        $this->paymentMethod->save();
        $this->alert("Bank has been created Successfully!..");
        $this->template_settings_value =  $this->paymentMethod->template_settings_value;
        $this->existingBanks = BankAccount::query()->with(['bank'])->whereIn('id', $this->template_settings_value)->get();
        $this->bank_id = "";
    }
}

?>
@section('pageHeaderTitle')
    {{ $this->paymentMethod->name }} Settings
@endsection

@push('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('backend.admin.settings.payment_methods') }}">Payment Methods</a></li>
    <li class="breadcrumb-item active">{{ $this->paymentMethod->name }} Settings</li>
@endpush

<div>
    <div class="card shadow-none border my-4">
        <div class="card-body p-0">
            <div class="p-4">
                <div class="row tx-14">
                    <div class="col-md-7">
                        <br/>
                        <h5>Bank List</h5>
                        <br/>
                        <div class="table-responsive scrollbar ms-n1 ps-1">
                            <table class="table table-sm fs-9 mb-0">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Bank Name</th>
                                    <th>Bank Account Name</th>
                                    <th>Bank Account Number</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($this->existingBanks as  $banks)
                                    <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                        <td class="align-middle text-wrap">{{ $loop->iteration }}</td>
                                        <td class="align-middle text-wrap">{{ $banks->bank->name }}</td>
                                        <td class="align-middle text-wrap">{{ $banks->account_name }}</td>
                                        <td class="align-middle text-wrap">{{ $banks->account_number }}</td>
                                        <td class="align-middle text-wrap">
                                            <a wire:click="deleteBank('{{ $banks->id }}')" class="btn btn-sm btn-danger">
                                                <span wire:loading wire:target="deleteBank('{{ $banks->id }}')" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                                <i wire:loading.remove wire:target="deleteBank('{{ $banks->id }}')" class="fa fa-trash"></i>
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
                        <br/>
                        <h5>Add New Bank</h5>
                        <br/>

                        <form id="validate" wire:submit.prevent="saveNewBank()">
                            <br/>
                            <div class="mb-3">
                                <label  class="form-label">Select Banks</label>
                                <x-dropdown-select-menu placeholder="Select Bank Accounts" wire:model="bank_id" :options="$this->bankAccounts" id="select_bank_account"/>
                                @error('bank_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="pull-left">
                                <button type="submit"  wire:target="saveNewBank" wire:loading.attr="disabled" class="btn btn-phoenix-success">
                                    <span wire:loading wire:target="saveNewBank" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    <i wire:loading.remove wire:target="saveNewBank" class="fa fa-save"></i>
                                    Save
                                </button>
                            </div>
                            <br/> <br/>  <br/> <br/>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
