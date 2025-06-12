<?php

use App\Classes\ApplicationEnvironment;
use App\Mail\Administrator\AdministratorInvitationMail;
use App\Mail\SalesRep\SalesRepInvitationMail;
use function Livewire\Volt\{state};
use Livewire\Volt\Component;
use Illuminate\Support\Facades\DB;
use App\Models\SalesRepresentative;
use Illuminate\Support\Facades\Session;
use App\Models\AppUser;
use App\Classes\AppLists;
use App\Models\User;

new class extends Component {
    public string $administrator = "";

    public string $successMessage = "";

    public function createAdministrator(): void
    {
        $model = new ApplicationEnvironment::$appModel();

        $this->validate(
            [
                'administrator' => 'required|numeric|unique:' . $model->modelTable . ',user_id',
            ],
            [
                'administrator.unique' => 'This user selected is already an administrator.',
            ]);
        $userAccountService = app(\App\Services\User\UserAccountService::class);
        DB::transaction(function () use ($model, $userAccountService) {
            $user = User::findorfail($this->administrator);
            $adminData = [
                'status' => "0",
                'user_id' => $this->administrator,
                'invitation_status' => "0",
                "invitation_sent_date" => now(),
                'added_by' => auth()->id(),
            ];

            $admin = match (get_class($model)) {
                \App\Models\SupermarketAdmin::class => $userAccountService->createSuperMarketAdministrator($user, $adminData),
                default => $userAccountService->createWholesalesAdministrator($user, $adminData)
            }

            Session::flash('status', 'An Invitation Email has been sent to ' . $user->email . " " . $user->name . " will become an administrator when they accept the invite  &#128513;");
        });
    }

    public function openModal(): void
    {
        $this->dispatch('openSalesRepModal');
    }


}
?>


@script
<script>
    const salesModal = bootstrap.Modal.getOrCreateInstance(document.getElementById("sales-rep-component"));

    function openSalesRepModal(e) {
        //let detail = e.detail[0];
        //let component = window.Livewire.find('{{ $this->getId() }}');
        salesModal.show();
    }

    function closeSalesRepModal(e) {
        if (e.detail !== null && e.detail[0].hasOwnProperty('status') && e.detail[0].status === true) {
            setTimeout(function () {
                salesModal.hide();
                window.location.reload();
            }, 3500)
        }
    }

    function forceCloseSalesRepModal(e) {
        salesModal.hide();
    }

    window.addEventListener('closeSalesRepModal', closeSalesRepModal);
    window.addEventListener('forceCloseSalesRepModal', forceCloseSalesRepModal);
    window.addEventListener('openSalesRepModal', openSalesRepModal);

</script>
@endscript


<div>
    <div wire:ignore.self class="modal fade" id="sales-rep-component" tabindex="-1" role="dialog" aria-hidden="true">
        <form method="post" wire:submit.prevent="createAdministrator">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        Add New Administrator
                        <button type="button" onclick="window.dispatchEvent(new CustomEvent('closeSalesRepModal'))"
                                class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12" id="modal-holder">
                                <div class="mb-3">
                                    <label class="form-label" for="salesRep_select2">Search For Users</label>
                                    <div class="wd-md-100p" id="salesRep_select2_parent">
                                        <x-dropdown-select-menu placeholder="Search For Users"
                                                                :value="$this->administrator"
                                                                edit-column="name" :edit-model="\App\Models\User::class"
                                                                wire:model="administrator" id="administrator"
                                                                :ajax="route('utilities.user.search')"/>
                                        <span class="text-danger">  @error('administrator'){{ $message }}@enderror</span>
                                    </div>
                                </div>
                            </div>
                            @if (session()->has('status'))
                                <div class="col-12">
                                    <div class="alert alert-success" role="alert">{!!  \session('status') !!}</div>
                                </div>
                            @endif
                        </div>
                        <br/>
                    </div>
                    <div class="modal-footer ">
                        <button type="submit" wire:target="createSalesRepresentatives" wire:loading.attr="disabled"
                                class="btn btn-phoenix-primary">
                            <span wire:loading wire:target="createSalesRepresentatives"
                                  class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Send Invitation
                        </button>
                        <button type="button" wire:target="createSalesRepresentatives" wire:loading.attr="disabled"
                                class="btn btn-phoenix-danger"
                                onclick="window.dispatchEvent(new CustomEvent('forceCloseSalesRepModal'))"
                                data-dismiss="modal" aria-label="Close">Cancel
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


