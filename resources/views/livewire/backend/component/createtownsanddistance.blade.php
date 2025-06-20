<?php

use Livewire\Volt\Component;
use function Livewire\Volt\{state};

new class extends Component {

    public array $towns = [];
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
    public array $deliveryDays =  [
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
            'type'=>'ALLOW_WHEN_CLOSE_TO_DATE',
            'interval_no' => "",
            'interval_frequency' => "",
            'starting_date' => ""
        ];
        $this->towns = towns()->toArray();

    }

    public function openModal(): void
    {
        $this->dispatch('openCreateTownsAndDistanceModal');
    }


    public function createTownsAndDistance()
    {
        $this->validate([
            'formData.town_id' => 'required',
            'formData.town_distance' => 'required',
            'formData.type' => 'required',
            'formData.minimum_shipping_amount' => 'required',
            'formData.fixed_shipping_amount' => 'required',

            'formData.no' => 'required_if:formData.type,0',
            'formData.frequency' => 'required_if:formData.type,0',
            'formData.delivery_days' => 'required_if:formData.type,0|array|min:1',


            'formData.starting_date'=> 'required_if:formData.type,1',
            'formData.interval_no'=> 'required_if:formData.type,1',
            'formData.interval_frequency'=> 'required_if:formData.type,1'
        ]);
    }

}
?>


@script
<script>
    const createTownsAndDistance = bootstrap.Modal.getOrCreateInstance(document.getElementById("create-towns-and-distance"));

    function openCreateTownsAndDistanceModal(e) {
        createTownsAndDistance.show();
    }

    function closeCreateTownsAndDistanceModal(e) {
        if (e.detail !== null && e.detail[0].hasOwnProperty('status') && e.detail[0].status === true) {
            setTimeout(function () {
                window.location.reload();
            }, 1500)
        }
        createTownsAndDistance.hide();
    }

    window.addEventListener('closeCreateTownsAndDistance', closeCreateTownsAndDistanceModal);
    window.addEventListener('openCreateTownsAndDistance', openCreateTownsAndDistanceModal);
    window.addEventListener('openCreateTownsAndDistanceModal', openCreateTownsAndDistanceModal);

    document.addEventListener('livewire:navigated',function(){
        Livewire.hook('morph.updated', ({ el, component }) => {
            if(el === document.getElementById('distanceType')){
                $('#distanceType').off('change');
                $('#distanceType').on('change', function(e){
                    if($('#distanceType').val() === "0") {
                        $('#delivery_days').removeClass('d-none');
                        $('#certain_days').addClass('d-none')
                        return ;
                    }

                    if($('#distanceType').val() === "1") {
                        $('#certain_days').removeClass('d-none');
                        $('#delivery_days').addClass('d-none');
                        return ;
                    }

                    $('#certain_days').addClass('d-none');
                    $('#delivery_days').addClass('d-none');
                })

                $('#distanceType').trigger('change')
            }
        })
    });
</script>
@endscript

<div>
    <div wire:ignore.self class="modal fade" id="create-towns-and-distance" tabindex="-1" role="dialog"
         aria-hidden="true">
        <form method="post" wire:submit.prevent="createTownsAndDistance">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Town And Distance</h5>
                        <button type="button"
                                onclick="window.dispatchEvent(new CustomEvent('closeCreateTownsAndDistance'))"
                                class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body ">
                        <div class="mb-3">
                            <label for="town_id" class="form-label">Town</label>
                            <x-dropdown-select-menu :options="$this->towns"  wire:model="formData.town_id" id="town_id_select" placeholder="Select Town"/>
                            @error("formData.town_id") <span class="text-danger d-block">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="distance" class="form-label">Distance</label>
                            <input type="number" step="0.00001" class="form-control" wire:model="formData.town_distance" id="town_distance" placeholder="Distance">
                            @error("formData.town_distance") <span class="text-danger d-block">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="minimum_shipping_amount" class="form-label">Minimum Delivery Cost</label>
                            <input type="number" class="form-control" wire:model="formData.minimum_shipping_amount" id="minimum_shipping_amount" placeholder="Minimum Delivery Cost">
                            @error("formData.minimum_shipping_amount") <span class="text-danger d-block">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="fixed_shipping_amount" class="form-label">Fixed Delivery Cost</label>
                            <input type="number" class="form-control" wire:model="formData.fixed_shipping_amount" id="fixed_shipping_amount" placeholder="Fixed Delivery Cost">
                            @error("formData.fixed_shipping_amount") <span class="text-danger d-block">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="fixed_shipping_amount" class="form-label">Town And Distance Type</label>
                            <select class="form-control" id="distanceType"  wire:model="formData.delivery_type">
                                <option>-Select Type-</option>
                                <option value="0">Days of the Week</option>
                                <option value="1">Every Certain Day</option>
                            </select>
                            @error("formData.fixed_shipping_amount") <span class="text-danger d-block">{{ $message }}</span> @enderror
                        </div>


                        <div id="delivery_days" class="d-none">
                            <div class="mb-3" >
                                <label for="days_week" class="form-label">Delivery Days</label>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="mb-0">
                                            <label for="no" class="form-label">No</label>
                                            <input type="number" class="form-control" wire:model="formData.no" id="no" placeholder="No">
                                            @error("formData.no") <span class="text-danger d-block">{{ $message }}</span> @enderror
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
                                            @error("formData.frequency") <span class="text-danger d-block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="delivery_days" class="form-label">Delivery Days</label>
                                @foreach($this->deliveryDays as $day)
                                    <div class="col-auto">
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" wire:model="formData.delivery_days" id="{{ $day['name'] }}" value="{{ $day['name'] }}" type="checkbox" />
                                            <label class="form-check-label mb-0" for="{{ $day['name'] }}">{{ $day['name']}}</label>
                                        </div>
                                    </div>
                                @endforeach
                                @error("formData.delivery_days") <span class="text-danger d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div  id="certain_days" class="d-none">
                            <div class="mb-3">
                                <label for="days_week" class="form-label">Delivery Intervals</label>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="mb-0">
                                            <label for="no" class="form-label">No</label>
                                            <input type="number" class="form-control" wire:model="formData.interval_no" id="interval_no" placeholder="No">
                                            @error("formData.interval_no") <span class="text-danger d-block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="mb-0">
                                            <label for="no" class="form-label">Frequency</label>
                                            <select class="form-control" wire:model="formData.interval_frequency" id="interval_frequency">
                                                <option value="">-Select Frequency-</option>
                                                <option value="days">Day(s)</option>
                                                <option value="week">Week(s)</option>
                                                <option value="month">Month(s)</option>
                                                <option value="year">Year(s)</option>
                                            </select>
                                            @error("formData.interval_frequency") <span class="text-danger d-block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3" id="">
                                <label for="delivery_days" class="form-label">Starting Date</label>
                                <input class="form-control" wire:model="formData.starting_date" id="basic-checkbox" type="date" />
                                @error("formData.starting_date") <span class="text-danger d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer ">
                        <button type="submit" wire:target="createTownsAndDistance" wire:loading.attr="disabled"
                                class="btn btn-primary">
                            <span wire:loading wire:target="createTownsAndDistance"
                                  class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Create
                        </button>
                        <button type="button" class="btn btn-danger"
                                onclick="window.dispatchEvent(new CustomEvent('closeCreateTownsAndDistance'))">Cancel
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
