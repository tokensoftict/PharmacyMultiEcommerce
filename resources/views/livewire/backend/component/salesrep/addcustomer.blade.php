<?php

use App\Mail\SalesRep\NewCustomer;
use function Livewire\Volt\{state};
use Livewire\Volt\Component;
use Illuminate\Support\Facades\DB;
use App\Models\SalesRepresentative;
use Illuminate\Support\Facades\Session;
use App\Models\AppUser;
use App\Classes\AppLists;
use App\Models\User;

new class extends Component {
    public string $customers = "";

    public string $successMessage = "";

    public SalesRepresentative $salesRepresentative;


    public function openModal(): void
    {
        $this->dispatch('openAddCustomerModal');
    }


    public function addCustomer()
    {
        $customer = \App\Models\WholesalesUser::find($this->customers);
        if($this->salesRepresentative->user->id === $customer->user->id) {
            Session::flash('error', "Sorry,  You can not add yourself under yourself!");
            return false;
        }
        $customer->sales_representative_id = $this->salesRepresentative->id;
        $customer->save();

        Session::flash('status', "$customer->business_name has been added under " . $this->salesRepresentative->user->name . " successfully  &#128513;");
        Mail::to($this->salesRepresentative->user->email)->send(new NewCustomer($this->salesRepresentative, $customer));
        $this->dispatch("closeAddCustomerModal", ['status' => true]);
    }

}
?>


@script
<script>
    const customerModal = bootstrap.Modal.getOrCreateInstance(document.getElementById("add-customer-component"));

    function openAddCustomerModal(e) {
        customerModal.show();
    }

    function closeAddCustomerModal(e) {
        if (e.detail !== null && e.detail[0].hasOwnProperty('status') && e.detail[0].status === true) {
            setTimeout(function () {
                customerModal.hide();
                window.location.reload();
            }, 3500)
        }
    }

    function forceCloseAddCustomerModal(e) {
        customerModal.hide();
    }

    window.addEventListener('closeAddCustomerModal', closeAddCustomerModal);
    window.addEventListener('openAddCustomerModal', openAddCustomerModal);
    window.addEventListener('forceCloseAddCustomerModal', forceCloseAddCustomerModal);
</script>
@endscript


<div>
    <div wire:ignore.self class="modal fade" id="add-customer-component" tabindex="-1" role="dialog" aria-hidden="true">
        <form method="post" wire:submit.prevent="addCustomer">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        Add New Customer
                        <button type="button" onclick="window.dispatchEvent(new CustomEvent('closeSalesRepModal'))"
                                class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12" id="modal-holder">
                                <div class="mb-3">
                                    <label class="form-label" for="addCustomer_select2">Search For Customer</label>
                                    <div class="wd-md-100p" id="addCustomer_select2_parent">
                                        <x-dropdown-select-menu placeholder="Search For Customers"
                                                                :value="$this->customers" edit-column="business_name"
                                                                :edit-model="\App\Models\WholesalesUser::class"
                                                                wire:model="customers" id="customers"
                                                                :ajax="route('utilities.user.wholesales.search')"/>
                                    </div>
                                </div>
                            </div>
                            @if (session()->has('error'))
                                <div class="col-12">
                                    <div class="alert alert-danger" role="alert">{!!  \session('error') !!}</div>
                                </div>
                            @endif
                            @if (session()->has('status'))
                                <div class="col-12">
                                    <div class="alert alert-success" role="alert">{!!  \session('status') !!}</div>
                                </div>
                            @endif
                        </div>
                        <br/>
                    </div>
                    <div class="modal-footer ">
                        <button type="submit" wire:target="addCustomer" wire:loading.attr="disabled"
                                class="btn btn-phoenix-primary">
                            <span wire:loading wire:target="addCustomer" class="spinner-border spinner-border-sm me-2"
                                  role="status"></span>
                            Add Customer
                        </button>
                        <button type="button" wire:target="addCustomer" wire:loading.attr="disabled"
                                class="btn btn-phoenix-danger"
                                onclick="window.dispatchEvent(new CustomEvent('forceCloseAddCustomerModal'))"
                                data-dismiss="modal" aria-label="Close">Cancel
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>


