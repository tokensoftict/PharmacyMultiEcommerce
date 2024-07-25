@script
<script>
    window.removeEventListener('closeUploadStockSize', closeUploadStockSize);
    window.removeEventListener('openUploadStockSize', openUploadStockSize);

    window.uploadstocksize = undefined;

    function openUploadStockSize(e)
    {
        window.uploadstocksize = bootstrap.Modal.getOrCreateInstance(document.getElementById("upload-stock-size"));

        document.getElementById("upload-stock-size").addEventListener('hidden.bs.modal', event => {
            window.uploadstocksize = null;
        })

        if( window.uploadstocksize._isShown === false) {
            window.uploadstocksize.show();
        }
    }

    function closeUploadStockSize(e)
    {
        if(e.detail !== null && e.detail[0].hasOwnProperty('status') && e.detail[0].status === true){
           setTimeout(function(){
               window.location.reload();
           }, 1500)
        }
        window.uploadstocksize.hide();
    }

    window.addEventListener('closeUploadStockSize', closeUploadStockSize);
    window.addEventListener('openUploadStockSize', openUploadStockSize);

</script>
@endscript

<div>
    <div wire:ignore.self class="modal fade" id="upload-stock-size" tabindex="-1" role="dialog" aria-hidden="true">
        <form method="post"  wire:submit.prevent="uploadStockSize">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload Stock Size</h5>
                        <button type="button"  class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body ">
                        <div class="mb-3">
                            <label class="form-label">Select File</label>
                            <input class="form-control" wire:model="file" type="file">
                        </div>
                        <a href="#" wire:click="downloadStockSize()">Download Stock Size Template</a>
                    </div>
                    <div class="modal-footer ">
                        <button type="submit" wire:target="uploadStockSize" wire:loading.attr="disabled" class="btn btn-phoenix-primary">
                            <span wire:loading wire:target="uploadStockSize" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Upload
                        </button>
                        <button type="button" class="btn btn-phoenix-danger" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
