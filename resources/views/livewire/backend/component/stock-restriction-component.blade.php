@script
<script>
    const restriction =  bootstrap.Modal.getOrCreateInstance(document.getElementById("stock-restriction-component"));

    function openRestrictionModal(e)
    {
        let detail = e.detail[0];
        let component = window.Livewire.find('{{ $this->getId() }}');
        window.Livewire.find('{{ $this->getId() }}').set('parameters', {group_id : detail.row.id, group_type : detail.type}, true);
        restriction.show();
    }

    function closeRestrictionModal(e)
    {
        if(e.detail !== null && e.detail[0].hasOwnProperty('status') && e.detail[0].status === true){
            setTimeout(function(){
                window.location.reload();
            }, 1500)
        }
        restriction.hide();
    }

    window.addEventListener('closeRestrictionModal', closeRestrictionModal);
    window.addEventListener('openRestrictionModal', openRestrictionModal);
</script>
@endscript
<div>
<div wire:ignore.self class="modal fade" id="stock-restriction-component" tabindex="-1" role="dialog" aria-hidden="true">
    <form method="post"  wire:submit.prevent="uploadRestriction">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Stock</h5>
                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('closeRestrictionModal'))" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body ">
                    <div class="mb-3">
                        <label class="form-label">Select File</label>
                        <input class="form-control" wire:model="file" type="file">
                    </div>
                    <a href="#" wire:click="downloadRestrictionTemplate()">Download Restriction Template</a>
                </div>
                <div class="modal-footer ">
                    <button type="submit" wire:target="uploadRestriction" wire:loading.attr="disabled" class="btn btn-phoenix-primary">
                        <span wire:loading wire:target="uploadRestriction" class="spinner-border spinner-border-sm me-2" role="status"></span>
                        Upload
                    </button>
                    <button type="button" class="btn btn-phoenix-danger" onclick="window.dispatchEvent(new CustomEvent('closeRestrictionModal'))">Cancel</button>
                </div>
            </div>
        </div>
    </form>
</div>
</div>
