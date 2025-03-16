@props(['options' => [],  'model', 'label'=>'Select File', 'id'=>generateRandomString(5), 'class', 'value'])

<div class="mb-3">
    <label class="form-label">{{ $label }}</label>
    <div class="input-group mb-3">
        <input type="text" readonly class="form-control" name="{{ $id }}"  wire:model="{{ $model }}" placeholder="{{ $label }}">
        <button class="btn btn-outline-primary" type="button" id="button{{ $id }}">{{ $label }}</button>
    </div>
    @error($model) <span class="text-danger">{{ $message }}</span> @enderror
</div>
<script>
    function button{{ $id }}(url) {
        @this.set('{{ $model }}', url);
    }
    document.addEventListener('livewire:navigated', function(){
        $('#button{{ $id }}').on('click', function(){
            window.openModalWithLoading(button{{ $id }});
        });

        if(window.openModalWithLoading === 'undefined') {
            window.openModalWithLoading = function (obj) {
                var opener = window.open('{{ route('file-manager.index') }}?parentLink={{  url('') }}', 'FileManager', 'width=900,height=650');

                function listenForMessage(event) {
                    obj(event.data);
                    window.removeEventListener("message", listenForMessage);
                }

            }
        }

    });

</script>