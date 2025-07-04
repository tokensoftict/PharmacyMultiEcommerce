<?php

use App\Models\DeliveryTownDistance;
use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new class extends Component {

    public array $towns = [];
    public array $parameters = [];
    public array $formData = [
        'town_id',
        'town_distance',
        'no',
        'frequency',
        'minimum_shipping_amount',
        'fixed_shipping_amount',
        'delivery_days',
        'delivery_type',
        'interval_no',
        'interval_frequency',
        'starting_date'
    ];
    public array $data = [];
    public array $deliveryDays = [
        [
            'name' => 'Monday',
            'id' => 'Monday'
        ],
        [
            'name' => 'Tuesday',
            'id' => 'Tuesday'
        ],
        [
            'name' => 'Wednesday',
            'id' => 'Wednesday'
        ],
        [
            'name' => 'Thursday',
            'id' => 'Thursday'
        ],
        [
            'name' => 'Friday',
            'id' => 'Friday'
        ],
        [
            'name' => 'Saturday',
            'id' => 'Saturday'
        ],
        [
            'name' => 'Sunday',
            'id' => 'Sunday'
        ],
    ];

    public function mount()
    {
        $this->formData = [
            'town_id' => "",
            'town_distance' => "",
            'no' => "",
            'frequency' => "",
            'minimum_shipping_amount' => "",
            'fixed_shipping_amount' => "",
            'delivery_days' => [],
            'delivery_type' => "",
            'type' => 'ALLOW_WHEN_CLOSE_TO_DATE',
            'interval_no' => "",
            'interval_frequency' => "",
            'starting_date' => ""
        ];
        $this->towns = towns()->map(function ($town) {
            return [
                'id' => $town->id,
                'text' => $town->name
            ];
        })->toArray();

    }

    public function openModal(): void
    {
        $this->dispatch('openEditTownsAndDistanceModal');
    }


    public function editTownsAndDistance()
    {
        $this->validate([
            'formData.town_id' => 'required',
            'formData.town_distance' => 'required',
            'formData.type' => 'required',
            'formData.delivery_type' => 'required',
            'formData.minimum_shipping_amount' => 'required',
            'formData.fixed_shipping_amount' => 'required',

            'formData.no' => 'required_if:formData.delivery_type,0',
            'formData.frequency' => 'required_if:formData.delivery_type,0',
            'formData.delivery_days' => 'required_if:formData.delivery_type,0',


            'formData.starting_date' => 'required_if:formData.delivery_type,1',
            'formData.interval_no' => 'required_if:formData.delivery_type,1',
            'formData.interval_frequency' => 'required_if:formData.delivery_type,1'
        ]);

        $this->formData['reset_time_days'] = 0;

        if ($this->formData['delivery_type'] == "1") {
            $this->formData['no'] = 0;
        }

        DeliveryTownDistance::updateOrCreate(
            ['id' => $this->formData['id']],
            $this->formData
        );
        Session::flash('status', "Town and distance has been updated successfully!");
        $this->dispatch("closeEditTownsAndDistanceModal", ['status' => true]);

    }


    public function openEditModal($row, $rowClass)
    {
        $this->parameters = $row;
        $this->formData = [
            'id' => $row['id'],
            'town_id' => $row['town_id'],
            'town_distance' => $row['town_distance'],
            'no' => $row['no'],
            'frequency' => $row['frequency'],
            'minimum_shipping_amount' => $row['minimum_shipping_amount'],
            'fixed_shipping_amount' => $row['fixed_shipping_amount'],
            'delivery_days' => $row['delivery_days'],
            'delivery_type' => $row['delivery_type'],
            'type' => 'ALLOW_WHEN_CLOSE_TO_DATE',
            'interval_no' => $row['interval_no'],
            'interval_frequency' => $row['interval_frequency'],
            'starting_date' => $row['starting_date']
        ];
        $this->dispatch('openEditTownsAndDistanceModal', ['type' => $rowClass, 'row' => $row]);
    }

}
?>


@script
<script>
    const editTownsAndDistance = bootstrap.Modal.getOrCreateInstance(document.getElementById("edit-towns-and-distance"));

    function openEditTownsAndDistanceModal(e) {
        let detail = e.detail[0]['row'];

        $('#editDistanceType').on('change', function (e) {
            showOrHide($('#editDistanceType').val())
        })

        function showOrHide(type) {
            if (type === "0") {
                $('#edit_delivery_days').removeClass('d-none');
                $('#edit_certain_days').addClass('d-none');
                return;
            }

            if (type === "1") {
                $('#edit_certain_days').removeClass('d-none');
                $('#edit_delivery_days').addClass('d-none');
                return;
            }

            $('#edit_certain_days').addClass('d-none');
            $('#edit_delivery_days').addClass('d-none');
        }

        document.getElementById("edit-towns-and-distance").addEventListener('shown.bs.modal', function () {
            showOrHide(detail['delivery_type']);
        })

        editTownsAndDistance.show();
    }

    function closeEditTownsAndDistanceModal(e) {
        if (e.detail !== null && e.detail[0].hasOwnProperty('status') && e.detail[0].status === true) {
            setTimeout(function () {
                window.location.reload();
                editTownsAndDistance.hide();
            }, 1200)
        }

    }

    function cancelEditModal(e) {
        editTownsAndDistance.hide();
    }

    window.addEventListener('closeEditTownsAndDistanceModal', closeEditTownsAndDistanceModal);
    window.addEventListener('openEditTownsAndDistanceModal', openEditTownsAndDistanceModal);
    window.addEventListener('openEditTownsAndDistanceModal', openEditTownsAndDistanceModal);
    window.addEventListener('cancelEditModal', cancelEditModal);

</script>
@endscript

<div>
    <div wire:ignore.self class="modal fade" id="edit-towns-and-distance" tabindex="-1" role="dialog"
         aria-hidden="true">
        <form method="post" wire:submit.prevent="editTownsAndDistance">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Town And Distance</h5>
                        <button type="button"
                                onclick="window.dispatchEvent(new CustomEvent('cancelEditModal'))"
                                class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body ">
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
                        <div class="mb-3">
                            <label for="town_id" class="form-label">Town</label>
                            <x-dropdown-select-menu :options="$this->towns" wire:model="formData.town_id"
                                                    id="town_id_select_edit" placeholder="Select Town"/>
                            @error("formData.town_id") <span class="text-danger d-block">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="distance" class="form-label">Distance</label>
                            <input type="number" step="0.00001" class="form-control" wire:model="formData.town_distance"
                                   id="town_distance" placeholder="Distance">
                            @error("formData.town_distance") <span
                                    class="text-danger d-block">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="minimum_shipping_amount" class="form-label">Minimum Delivery Cost</label>
                            <input type="number" class="form-control" wire:model="formData.minimum_shipping_amount"
                                   id="minimum_shipping_amount" placeholder="Minimum Delivery Cost">
                            @error("formData.minimum_shipping_amount") <span
                                    class="text-danger d-block">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="fixed_shipping_amount" class="form-label">Fixed Delivery Cost</label>
                            <input type="number" class="form-control" wire:model="formData.fixed_shipping_amount"
                                   id="fixed_shipping_amount" placeholder="Fixed Delivery Cost">
                            @error("formData.fixed_shipping_amount") <span
                                    class="text-danger d-block">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="fixed_shipping_amount" class="form-label">Town And Distance Type</label>
                            <select class="form-control" id="editDistanceType" wire:model="formData.delivery_type">
                                <option value="">-Select Type-</option>
                                <option value="0">Days of the Week</option>
                                <option value="1">Every Certain Day</option>
                            </select>
                            @error("formData.fixed_shipping_amount") <span class="text-danger d-block">{{ $message }}</span> @enderror
                        </div>


                        <div id="edit_delivery_days" class="d-none">
                            <div class="mb-3">
                                <label for="days_week" class="form-label">Delivery Days</label>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="mb-0">
                                            <label for="no" class="form-label">No</label>
                                            <input type="number" class="form-control" wire:model="formData.no" id="no"
                                                   placeholder="No">
                                            @error("formData.no") <span
                                                    class="text-danger d-block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="mb-0">
                                            <label for="no" class="form-label">Frequency</label>
                                            <select class="form-control" wire:model="formData.frequency" id="frequency">
                                                <option value="">-Select Frequency-</option>
                                                <option value="days">Day(s)</option>
                                                <option value="week">Week(s)</option>
                                                <option value="month">Month(s)</option>
                                                <option value="year">Year(s)</option>
                                            </select>
                                            @error("formData.frequency") <span
                                                    class="text-danger d-block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="delivery_days" class="form-label">Delivery Days</label>
                                @foreach($this->deliveryDays as $day)
                                    <div class="col-auto">
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" wire:model="formData.delivery_days"
                                                   id="{{ $day['name'] }}" value="{{ $day['name'] }}" type="checkbox"/>
                                            <label class="form-check-label mb-0"
                                                   for="{{ $day['name'] }}">{{ $day['name']}}</label>
                                        </div>
                                    </div>
                                @endforeach
                                @error("formData.delivery_days") <span
                                        class="text-danger d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div id="edit_certain_days" class="d-none">
                            <div class="mb-3">
                                <label for="days_week" class="form-label">Delivery Intervals</label>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="mb-0">
                                            <label for="no" class="form-label">No</label>
                                            <input type="number" class="form-control" wire:model="formData.interval_no"
                                                   id="interval_no" placeholder="No">
                                            @error("formData.interval_no") <span
                                                    class="text-danger d-block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="mb-0">
                                            <label for="no" class="form-label">Frequency</label>
                                            <select class="form-control" wire:model="formData.interval_frequency"
                                                    id="interval_frequency">
                                                <option value="">-Select Frequency-</option>
                                                <option value="days">Day(s)</option>
                                                <option value="week">Week(s)</option>
                                                <option value="month">Month(s)</option>
                                                <option value="year">Year(s)</option>
                                            </select>
                                            @error("formData.interval_frequency") <span
                                                    class="text-danger d-block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3" id="">
                                <label for="delivery_days" class="form-label">Starting Date</label>
                                <input class="form-control" wire:model="formData.starting_date" id="basic-checkbox" type="date"/>
                                @error("formData.starting_date") <span class="text-danger d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer ">
                        <button type="submit" wire:target="editTownsAndDistance" wire:loading.attr="disabled"
                                class="btn btn-primary">
                            <span wire:loading wire:target="editTownsAndDistance"
                                  class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Update
                        </button>
                        <button type="button" class="btn btn-danger"
                                onclick="window.dispatchEvent(new CustomEvent('cancelEditModal'))">Cancel
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
