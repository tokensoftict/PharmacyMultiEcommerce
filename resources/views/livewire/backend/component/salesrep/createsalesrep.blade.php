<?php

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
    public string $salesRep = "";

    public string $successMessage = "";

    public function createSalesRepresentatives(): void
    {
        $this->validate(
            [
                'salesRep' => 'required|numeric|unique:sales_representatives,user_id',
            ],
            [
                'salesRep.unique' => 'This user selected is already a sales representative.',
            ]);
        DB::transaction(function () {
            $user = User::findorfail($this->salesRep);
            $rep = SalesRepresentative::updateOrCreate([
                'user_id' => $this->salesRep,
            ],
                [
                    'status' => "0",
                    'user_id' => $this->salesRep,
                    'invitation_status' => "0",
                    "invitation_sent_date" => now(),
                    'added_by' => auth()->id(),
                    'token' => sha1(md5(generateRandomString(50))),
                    'code'  => generateUniqueReferralCode()
                ]);

            AppUser::updateOrCreate(
                [
                    'user_id' =>  $this->salesRep,
                    'user_type_type' => SalesRepresentative::class,
                    'app_id' => 4,
                    'domain' => AppLists::getApp($rep)
                ],
                [
                    'user_type_id' =>  $rep->id,
                    'user_type_type' => SalesRepresentative::class,
                    'app_id' => 4,
                    'domain' => AppLists::getApp($rep),
                    'user_id' =>  $this->salesRep
                ]
            );
            //trigger invent to send invite email to the user
            $link = route('sales-representative.sales_rep.accept-invitation', $rep->token);
            Mail::to($rep->user->email)->send(new SalesRepInvitationMail($rep, $link));
            Session::flash('status', 'An Invitation Email has been sent to ' . $user->email . " " . $user->name . " will become a sales representative when they accept the invite  &#128513;");

            $this->dispatch("closeSalesRepModal", ['status' => true]);
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

    window.addEventListener('closeSalesRepModal', closeSalesRepModal);
    window.addEventListener('openSalesRepModal', openSalesRepModal);

</script>
@endscript


<div>
    <div wire:ignore.self class="modal fade" id="sales-rep-component" tabindex="-1" role="dialog" aria-hidden="true">
        <form method="post" wire:submit.prevent="createSalesRepresentatives">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        Create a New Sales Representatives
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
                                        <x-dropdown-select-menu placeholder="Search For Users" :value="$this->salesRep"
                                                                edit-column="name" :edit-model="\App\Models\User::class"
                                                                wire:model="salesRep" id="sales_rep"
                                                                :ajax="route('utilities.user.search')"/>
                                        <span class="text-danger">  @error('salesRep')
                                            {{ $message }}
                                            @enderror</span>
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
                                onclick="window.dispatchEvent(new CustomEvent('closeSalesRepModal'))"
                                data-dismiss="modal" aria-label="Close">Cancel
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


