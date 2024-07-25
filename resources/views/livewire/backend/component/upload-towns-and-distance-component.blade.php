@script
<script>
    const uploadtownsanddistance =  bootstrap.Modal.getOrCreateInstance(document.getElementById("upload-towns-and-distance"));

    function openUploadTownsAndDistanceModal(e)
    {
        uploadtownsanddistance.show();
    }

    function closeUploadTownsAndDistanceModal(e)
    {
        if(e.detail !== null && e.detail[0].hasOwnProperty('status') && e.detail[0].status === true){
           setTimeout(function(){
               window.location.reload();
           }, 1500)
        }
        uploadtownsanddistance.hide();
    }

    window.addEventListener('closeUploadTownsAndDistance', closeUploadTownsAndDistanceModal);
    window.addEventListener('openUploadTownsAndDistance', openUploadTownsAndDistanceModal);
</script>
@endscript

<div>
    <div wire:ignore.self class="modal fade" id="upload-towns-and-distance" tabindex="-1" role="dialog" aria-hidden="true">
        <form method="post"  wire:submit.prevent="uploadTownsAndDistance">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload Towns And Distances</h5>
                        <button type="button" onclick="window.dispatchEvent(new CustomEvent('closeUploadTownsAndDistance'))" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body ">
                        <div class="mb-3">
                            <label class="form-label">Select File</label>
                            <input class="form-control" wire:model="file" type="file">
                        </div>
                        <a href="#" wire:click="downloadTownAndDeliveryTemplate()">Download Towns And Distances Template</a>
                    </div>
                    <div class="modal-footer ">
                        <button type="submit" wire:target="uploadTownsAndDistance" wire:loading.attr="disabled" class="btn btn-primary">
                            <span wire:loading wire:target="uploadTownsAndDistance" class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Upload
                        </button>
                        <button type="button" class="btn btn-danger" onclick="window.dispatchEvent(new CustomEvent('closeUploadTownsAndDistance'))">Cancel</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
