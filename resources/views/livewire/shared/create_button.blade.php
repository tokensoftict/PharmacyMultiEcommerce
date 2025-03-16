@if(isset($this->actionPermission['create']) && userCanView($this->actionPermission['create']))
    <button  wire:click="create" wire:target="create" wire:loading.attr="disabled" type="button" class="btn btn-outline-success">
        <i wire:loading.remove wire:target="create" class="fa fa-plus"></i>
        <span wire:loading wire:target="create" class="spinner-border spinner-border-sm me-2" role="status"></span>
        New {{ $this->modalName }}
    </button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
@endif


@if(isset($this->actionPermission['create_link']) && userCanView($this->actionPermission['create_link']['route']))
    <a  href="{{ route($this->actionPermission['create_link']['route']) }}" wire:navigate type="button" class="btn btn-outline-success">
        <i class="fa fa-plus"></i>
        {{ $this->actionPermission['create_link']['label'] }}
    </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
@endif
@php
//Livewire.getByName('{{ $toolbarButton['modal'] }}')[0].openModal(); return false;
 @endphp

@if(isset($this->toolbarButtons))
    @foreach($this->toolbarButtons as $key => $toolbarButton)
        @if(userCanView($this->actionPermission[$toolbarButton['permission']]))
        <a href="#" type="button" onclick="Livewire.getByName('{{ $toolbarButton['modal'] }}')[0].openModal(); return false;" class="{{ $toolbarButton['class'] }}">
            <i wire:loading.remove wire:target="{{ $toolbarButton['modal'] }}.openModal" class="{{ $toolbarButton['icon'] ?? 'fa fa-trash-alt' }}"></i><span wire:loading wire:target="" class="spinner-border spinner-border-sm me-2" role="status"></span>
             {{ $toolbarButton['label'] }}
        </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        @endif
    @endforeach
@endif
@if(env('APP_DEBUG'))
<script>
    document.addEventListener('livewire:navigated', function(){
        console.log( Livewire.all());
    })
@endif
</script>