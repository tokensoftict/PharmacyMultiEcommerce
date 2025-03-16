@section('pageHeaderTitle')
    {{  $pageHeaderTitle ?? "" }}
@endsection

@section('filterTable')
    {!! $filterTable  !!}
@endsection

@push('breadcrumbs')
    @if(isset($this->breadcrumbs))
        @foreach($this->breadcrumbs as $breadcrumb)
            @if($breadcrumb['active'] === true)
                <li class="breadcrumb-item active">{{ $breadcrumb['name'] }}</li>
            @else
                <li class="breadcrumb-item"><a href="{{ $breadcrumb['route'] }}">{{ $breadcrumb['name'] }}</a></li>
            @endif
        @endforeach
    @endif
@endpush


<div  wire:ignore.self class="modal fade" id="simpleComponentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <form method="post" wire:submit.prevent="{{ $this->modalTitle === "New" ? 'save()' : 'update('.$this->modelId.')' }}">
        <div class="modal-dialog modal-dialog-centered" role="document">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">{{ $this->modalTitle }} {{ $this->modalName }}</h5>
                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('closeModal'))" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-12" id="modal-holder">
                            @foreach($this->data as $key=>$value)
                                @if($value['type'] === "text" || $value['type'] === "email" || $value['type'] === "number")
                                    <div class="mb-3">
                                        <label class="form-label">{{ $value['label'] }}</label>
                                        <input class="form-control" type="{{ $value['type'] }}"  wire:model="formData.{{ $key }}"  name="{{ $key }}"  placeholder="{{ $value['label'] }}">
                                        @error("formData.".$key) <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                @endif

                                @if($value['type'] === "password")
                                    <div class="mb-3">
                                        <label class="form-label">{{ $value['label'] }}</label>
                                        <input class="form-control" type="{{ $value['type'] }}" wire:model="formData.{{ $key }}"  name="{{ $key }}"  placeholder="{{ $value['label'] }}">
                                        @if($this->modalTitle == "Update")
                                            <span class="text-info d-block">Leave Blank if you don't want to change password</span>
                                        @endif
                                        @error("formData.".$key) <span class="text-danger d-block">{{ $message }}</span> @enderror
                                    </div>
                                @endif
                                @if($value['type'] === "dropDownSelect" or $value['type'] === "select")
                                    <div class="mb-3">
                                        <label class="form-label">{{ $value['label'] }}</label>
                                        <div class="wd-md-100p" id="select2Parent{{ $key }}">
                                            <x-dropdown-select-menu :options="$value['options']" wire:model="formData.{{ $key }}" :id="$key"  placeholder="{{ $value['label'] }}"/>
                                        </div>
                                    </div>
                                    @error("formData.".$key) <span class="text-danger">{{ $message }}</span> @enderror
                                @endif

                                @if($value['type'] === "ajax-dropdown")
                                    <div class="mb-3">
                                        <label class="form-label">{{ $value['label'] }}</label>
                                        <div class="wd-md-100p" id="select2Parent{{ $key }}">
                                            <x-dropdown-select-menu placeholder="{{ $value['label'] }}" wire:model="formData.{{ $key }}" :id="$key" :ajax="$value['search-route'] ?? route('utilities.user.search')"/>
                                        </div>
                                    </div>
                                    @error("formData.".$key) <span class="text-danger">{{ $message }}</span> @enderror
                                @endif


                                @if($value['type'] === "textarea")
                                    <div class="mb-3">
                                        <label class="form-label">{{ $value['label'] }}</label>
                                        <textarea class="form-control" name="{{ $key }}"  wire:model="formData.{{ $key }}"  placeholder="{{ $value['label'] }}"></textarea>
                                        @error("formData.".$key) <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                @endif

                                @if($value['type'] === "hidden")
                                    <div class="mb-3">
                                        @if(isset($value['showValue']) && $value['showValue'] == true)
                                            <label class="form-label">{{ $value['label'] }}</label>
                                            <span class="form-control">{{ $value['editDisplay'] ??  $value['display']  }}</span>
                                        @endif
                                        <input type="hidden" wire:model="formData.{{ $key }}">
                                        @error("formData.".$key) <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                @endif

                                @if($value['type'] === "image")
                                    <div class="mb-3">
                                        <label class="form-label">{{ $value['label'] }}</label>
                                        <div class="input-group mb-3">
                                            <input type="text" readonly class="form-control" name="{{ $key }}"  wire:model="formData.{{ $key }}" placeholder="{{ $value['label'] }}">
                                            <button class="btn btn-outline-primary" type="button" id="button{{ $key }}">Select Image</button>
                                        </div>
                                        @error("formData.".$key) <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <script>
                                        function button{{ $key }}(url) {
                                            @this.set('formData.{{ $key }}', url);
                                        }
                                        document.addEventListener('livewire:navigated', function(){
                                            $('#button{{ $key }}').on('click', function(){
                                                openModalWithLoading(button{{ $key }});
                                            });
                                        });
                                    </script>
                                @endif

                                @if($value['type'] === "stockComponent")
                                    <div class="mb-3">
                                        <label class="form-label">{{ $value['label'] }}</label>
                                        <div class="wd-md-100p" id="select2Parent{{ $key }}">
                                            <x-utilities.general.stock-search-component :placeholder="$value['placeholder']" :classname="''" :wireModel="'formData.'.$key" :id="$value['id']"/>
                                        </div>
                                        @error("formData.".$key) <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                @endif

                                @if($value['type'] == "datepicker")
                                    <div class="mb-3">
                                        <label class="form-label">{{ $value['label'] }}</label>
                                        <input class="form-control" type="date"  wire:model="formData.{{ $key }}"  name="{{ $key }}"  placeholder="{{ $value['label'] }}">
                                        @error("formData.".$key) <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                @endif

                                @if($value['type'] == "file")
                                    <div class="mb-3">
                                        <label class="form-label">{{ $value['label'] }}</label>
                                        <input class="form-control" type="file" wire:model="formData.{{ $key }}" name="{{ $key }}" placeholder="{{ $value['label'] }}" >
                                        @if(isset($value['template']))
                                            <a href="#" wire:click="{{ $value['template'] }}()">{{ $value['templateLabel'] }}</a>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>

                    </div>
                </div>
                <div class="modal-footer ">
                    <button type="submit" wire:target="save,update" wire:loading.attr="disabled" class="btn btn-phoenix-primary">
                        <span wire:loading wire:target="save,update" class="spinner-border spinner-border-sm me-2" role="status"></span>
                        {{ $this->saveButton }}
                    </button>
                    <button type="button" wire:target="uploadRestriction" wire:loading.attr="disabled" class="btn btn-phoenix-danger" onclick="window.dispatchEvent(new CustomEvent('closeModal'))" data-dismiss="modal" aria-label="Close">Cancel</button>
                </div>
            </div>

        </div>
    </form>
</div>
<script>
    if( typeof myModal === 'undefined') {
        let myModal = "";
    }else{
        myModal = ""
    }


    function openModalWithLoading(obj)
    {
        var opener = window.open('{{ route('file-manager.index') }}?parentLink={{  url('') }}', 'FileManager', 'width=900,height=650');
        function listenForMessage(event) {
            obj(event.data);
            window.removeEventListener("message", listenForMessage);
        }
        window.addEventListener("message", listenForMessage);
    }



    function hydrated()
    {
        myModal = new bootstrap.Modal(document.getElementById("simpleComponentModal"), {
            backdrop: 'static',
            keyboard: false,
            focus: true
        });

        window.removeEventListener('openModal', openModal)
        window.addEventListener('openModal', openModal);

        window.removeEventListener('closeModal', closeModal)
        window.addEventListener('closeModal', closeModal);

        document.removeEventListener('livewire:navigated', hydrated);
    }

    function openModal(e)
    {

        if(e.detail[0].hasOwnProperty('clearField') && e.detail[0].clearField === true){
            $('.select2').each(function (i, obj) {
                $(obj).val("");
            });
        }

        myModal.show();
    }

    function closeModal(e)
    {
        myModal.hide();
    }

    function unhydrated()
    {
        myModal.dispose();
    }

    document.removeEventListener('livewire:navigated', hydrated);
    document.addEventListener('livewire:navigated', hydrated)
</script>
