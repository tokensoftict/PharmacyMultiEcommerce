<?php

use App\Classes\ApplicationEnvironment;
use App\Services\Utilities\PushNotificationService;
use Jantinnerezo\LivewireAlert\Enums\Position;
use App\Models\Stock;
use function Livewire\Volt\{state};
use Livewire\Volt\Component;
use Illuminate\Support\Facades\DB;
use App\Models\SalesRepresentative;
use Illuminate\Support\Facades\Session;
use App\Models\AppUser;
use App\Classes\AppLists;
use App\Models\User;
use App\Services\Api\MedReminder\MedReminderService;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

new class extends Component {
    public string $salesRep = "";

    public string $successMessage = "";

    public string $user_id, $stock_id, $total_dosage_in_package, $type, $dosage, $interval = "";
    public string $every, $start_date_time, $drug_name = "";


    public array $normal_schedules = [
        'Morning' => "08:00",
        'Afternoon' => "12:00",
        'Evening' => "20:00",
        'Mid_Night' => "00:00"
    ];

    public bool $use_interval = false;

    public function createMedReminder(): void
    {
        $validate = $this->validate([
            'user_id' => 'required|exists:users,id',
            'stock_id' => 'required|exists:stocks,id',
            //'drug_name' => 'required',
            'dosage' => 'required',
            'total_dosage_in_package' => 'required',
            'type' => 'required',
            'use_interval' => 'required',
            'interval' => 'required_if:use_interval,1',
            'every' => 'required_if:use_interval,1',
            'start_date_time' => 'required',
            'normal_schedules' => 'required_if:use_interval,0',
        ]);

        $validate['drug_name'] = Stock::find($this->stock_id)->name;

        $medService = new MedReminderService();
        if (isset($validate['normal_schedules']['Mid_Night'])) {
            $validate['normal_schedules']['Mid-Night'] = $validate['normal_schedules']['Mid_Night'];
            unset($validate['normal_schedules']['Mid_Night']);
        }

        $medService = $medService->create($validate);

        $this->alert(
            "success",
            "Med Schedule has been created successfully"
        );

        $this->dispatch('closeMedReminderModal');

        LivewireAlert::title("Med Reminder Push Notification")
            ->text("Do you want to push this med reminder schedules to user's phone")
            ->withConfirmButton('Yes Please')
            ->withCancelButton('No Thanks')
            ->onConfirm('pushMedToDevice', ['id' => $medService->id])
            ->onDismiss("refreshPage", [])
            ->timer(0)
            ->show();
    }

    public function openModal(): void
    {
        $this->dispatch('openMedReminderModal');
    }


    public function pushMedToDevice($data)
    {
        $id = $data['id'];
        (new MedReminderService())->pushSchedulesToUsersPhone($id);
    }


    public function refreshPage()
    {
        $this->dispatch('refreshPage');
    }


}
?>


@script
<script>
    const salesModal = bootstrap.Modal.getOrCreateInstance(document.getElementById("med-reminder-component"));

    function openMedReminderModal(e) {
        //let detail = e.detail[0];
        //let component = window.Livewire.find('{{ $this->getId() }}');
        salesModal.show();
    }

    function refreshPage() {
        window.location.reload();
    }

    function closeMedReminderModal(e) {
        setTimeout(function () {
            salesModal.hide();
        }, 1500)
    }

    window.addEventListener('closeMedReminderModal', closeMedReminderModal);
    window.addEventListener('openMedReminderModal', openMedReminderModal);
    window.addEventListener('refreshPage', refreshPage);

</script>
@endscript


<div>
    <div wire:ignore.self class="modal fade" id="med-reminder-component" tabindex="-1" role="dialog" aria-hidden="true">
        <form method="post" wire:submit.prevent="createMedReminder">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        Create a Med Reminder
                        <button type="button" onclick="window.dispatchEvent(new CustomEvent('closeMedReminderModal'))"
                                class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12" id="modal-holder">
                                <div class="mb-3">
                                    <label class="form-label" for="salesRep_select2">Select Customers</label>
                                    <div class="wd-md-100p" id="salesRep_select2_parent">
                                        <x-dropdown-select-menu placeholder="Search Customer" wire:model="user_id"
                                                                id="user_id" :ajax="route('utilities.user.search')"/>
                                        @error("user_id") <span
                                                class="text-danger d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="salesRep_select2">Select Stock</label>
                                    <div class="wd-md-100p" id="salesRep_select2_parent">
                                        <x-dropdown-select-menu placeholder="Search Stock" wire:model="stock_id"
                                                                id="stock_id"
                                                                :ajax="route('utilities.stock.select2search')"/>
                                        @error("stock_id") <span
                                                class="text-danger d-block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="total_dosage_in_package">Total Dosage in
                                        Package</label>
                                    <input type="text" class="form-control" wire:model="total_dosage_in_package"
                                           id="total_dosage_in_package" placeholder="Total Dosage in Package">
                                    @error("total_dosage_in_package") <span
                                            class="text-danger d-block">{{ $message }}</span> @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="dosage">Dosage</label>
                                    <input type="text" class="form-control" wire:model="dosage" id="dosage"
                                           placeholder="Dosage to be taken">
                                    @error("dosage") <span class="text-danger d-block">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="type">Med Type</label>
                                    <x-dropdown-select-menu wire:model="type" id="type"
                                                            :options="[['id'=>'ONE-TIME', 'text' => 'ONE TIME'], ['id' => 'CONTINUES', 'text' => 'CONTINUES']]"/>
                                    @error("type") <span class="text-danger d-block">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" onchange="toggleInterval(this)"
                                               wire:model="use_interval" id="flexSwitchCheckChecked" type="checkbox"/>
                                        <label class="form-check-label" for="flexSwitchCheckChecked">Use
                                            Interval</label>
                                    </div>
                                </div>

                                <div id="useInterval" style="display: none">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon3">Every</span>
                                        <input class="form-control" wire:model="interval" type="number">
                                        <input type="hidden" wire:model="every" id="every">
                                        <div class="dropdown">

                                            <button class="btn btn-phoenix-secondary dropdown-toggle"
                                                    id="dropdownMenuInterval" type="button" data-bs-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">&nbsp;&nbsp;&nbsp;&nbsp;
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                                <button class="dropdown-item" type="button"
                                                        onclick="selectInterval('hours', this)">Hours
                                                </button>
                                                <button class="dropdown-item" type="button"
                                                        onclick="selectInterval('days', this)">Days
                                                </button>
                                                <button class="dropdown-item" type="button"
                                                        onclick="selectInterval('weeks', this)">Weeks
                                                </button>
                                                <button class="dropdown-item" type="button"
                                                        onclick="selectInterval('months', this)">Months
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @error("interval") <span
                                            class="text-danger d-block">{{ $message }}</span><br/> @enderror
                                    @error("every") <span
                                            class="text-danger d-block">{{ $message }}</span><br/> @enderror
                                </div>
                                <div id="useSchedules">
                                    <label class="form-label" for="basic-url">Schedules</label>
                                    <div class="input-group input-group-sm mb-3">
                                        <span class="input-group-text"
                                              id="inputGroup-sizing-sm">Morning&nbsp;&nbsp;&nbsp;</span>
                                        <input class="form-control" wire:model="normal_schedules.Morning" type="time"
                                               aria-label="Sizing example input"
                                               aria-describedby="inputGroup-sizing-sm"/>
                                    </div>
                                    <div class="input-group input-group-sm mb-3">
                                        <span class="input-group-text"
                                              id="inputGroup-sizing-sm">Afternoon</span>
                                        <input class="form-control" wire:model="normal_schedules.Afternoon" type="time"
                                               aria-label="Sizing example input"
                                               aria-describedby="inputGroup-sizing-sm"/>
                                    </div>
                                    <div class="input-group input-group-sm mb-3">
                                        <span class="input-group-text"
                                              id="inputGroup-sizing-sm">Evening&nbsp;&nbsp;&nbsp;</span>
                                        <input class="form-control" wire:model="normal_schedules.Evening" type="time"
                                               aria-label="Sizing example input"
                                               aria-describedby="inputGroup-sizing-sm"/>
                                    </div>
                                    <div class="input-group input-group-sm mb-3">
                                        <span class="input-group-text"
                                              id="inputGroup-sizing-sm">Mid-Night</span>
                                        <input class="form-control" wire:model="normal_schedules.Mid_Night" type="time"
                                               aria-label="Sizing example input"
                                               aria-describedby="inputGroup-sizing-sm"/>
                                    </div>
                                    @error("every") <span
                                            class="text-danger d-block">{{ $message }}</span><br/> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="type">Starting Date and Time</label>
                                    <input type="datetime-local" class="form-control" wire:model="start_date_time"
                                           id="start_date_time" placeholder="Starting Date and Time">
                                    @error("start_date_time") <span
                                            class="text-danger d-block">{{ $message }}</span><br/> @enderror
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
                        <button type="submit" wire:target="createMedReminder" wire:loading.attr="disabled"
                                class="btn btn-phoenix-primary">
                            <span wire:loading wire:target="createMedReminder"
                                  class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Save
                        </button>
                        <button type="button" wire:target="createMedReminder" wire:loading.attr="disabled"
                                class="btn btn-phoenix-danger"
                                onclick="window.dispatchEvent(new CustomEvent('closeMedReminderModal'))"
                                data-dismiss="modal" aria-label="Close">Cancel
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script>
        function selectInterval(interval, btn) {
            $('#dropdownMenuInterval').html($(btn).html());
            @this.
            set('every', interval, false);
        }

        function toggleInterval(checkBox) {
            if ($(checkBox).is(':checked')) {
                $('#useInterval').removeAttr('style');
                $('#useSchedules').attr('style', 'display:none');
            } else {
                $('#useSchedules').removeAttr('style');
                $('#useInterval').attr('style', 'display:none');
            }
        }
    </script>
</div>