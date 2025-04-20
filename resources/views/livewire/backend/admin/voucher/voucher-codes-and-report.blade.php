
@script
<script>
    window.removeEventListener('refreshPage', refreshPage);

    function refreshPage() {
        setTimeout(function(){
            window.location.reload();
        }, 1000 )
    }

    window.addEventListener('refreshPage', refreshPage);

</script>
@endscript

<div>

    <div class="row align-items-center justify-content-between g-3 mt-n5">
        <div class="col-auto">
            <h2 class="mb-0">Voucher Codes and Report</h2>
        </div>
        <div class="col-auto">
            <div class="row g-3">
                @if($this->voucher->status_id === status('Pending') || $this->voucher->status === status('Cancelled'))
                    <div class="col-auto"><button wire:click="approveVoucher"  wire:confirm="Are you sure you want approved this voucher ?" class="btn btn-primary"><span class="fa-solid fa-check"></span> Approve</button></div>
                @endif
                @if($this->voucher->status_id === status('Approved'))
                    <div class="col-auto"><button wire:click="cancelVoucher"  wire:confirm="Are you sure you want cancelled this voucher ?" class="btn btn-warning"><span class="fa-solid fa-stop"></span> Cancelled</button></div>
                @endif
                @if($this->voucher->status_id !== status('Pending') || $this->voucher->status === status('Cancelled'))
                    <div class="col-auto"><button wire:click="deleteVoucher"  wire:confirm="Are you sure you want approved this voucher ?" class="btn btn-danger"><span class="fa-solid fa-stop"></span> Delete</button></div>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-5 mt-2">
        <div class="col-12">
            <div class="row g-3 h-100">
                <div class="col-12 col-md-12 col-xxl-12">
                    @livewire("backend.admin.voucher.voucher-codes-datatable", ['id' => $this->id] )
                </div>
            </div>
        </div>
    </div>
</div>
