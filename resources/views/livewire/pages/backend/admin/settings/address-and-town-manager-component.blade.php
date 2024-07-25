<div>
    <div class="row row-xs">
        <div class="col-lg-4 col-12">
            <label for="state">Select State</label>
            <select wire:model="state_id" wire:change="fetchLGA" class="form-select" id="state">
                <option value="">Select State</option>
                @foreach($this->states as $state)
                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-4 col-12">
            <label for="lga">Select LGA</label>
            <select wire:model="lga_id" class="form-select" id="lga">
                @if($this->lgas)
                    <option value="">Select Local Govt.</option>
                    @foreach($this->lgas as $lga)
                        <option value="{{ $lga->id }}">{{ $lga->name }}</option>
                    @endforeach
                @else
                    <option value="">Select State First</option>
                @endif
            </select>
        </div>
        <div class="col-lg-4 col-12">
            <div class="mt-3"></div>
            <button wire:click="filterTown" wire:target="filterTown" wire:loading.attr="disabled" type="button" class="btn btn-phoenix-primary mt-1">
                Submit
                <i wire:target="filterTown" wire:loading class="fa fa-spinner  fa-spin"></i>
            </button>
        </div>
    </div>

    @if($this->state_id != "" && $this->lga_id != "")
        <div class="row row-xs mt-5">
            <livewire:pages.backend.admin.component.address-and-town-data-table-component :state_id="$this->state_id" :lga_id="$this->lga_id"/>
        </div>
    @endif
</div>
