@props(['options' => [], 'model', 'id'=>generateRandomString(5), 'class', 'placeholder'=>"Select Item", 'value', 'ajax', 'editModel', 'editColumn'])

<style>
    .no-hover{
        color: transparent !important;
        text-decoration: none;
        background-color:transparent !important;
    }

    .btn-select-toggle::after {
        display: inline-block;
        margin-left: 0 !important;
        margin-top: 6px;
        float: right;
        vertical-align: .255em;
        content: "";
        border-top: .4em solid;
        border-right: .4em solid rgba(0, 0, 0, 0);
        border-bottom: 0;
        border-left: .4em solid rgba(0, 0, 0, 0);
    }
</style>


<div>
    <script type="text/javascript">

        function debounce(func, wait, immediate) {
            var timeout;
            return function() {
                var context = this, args = arguments;
                var later = function() {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        };

        document.addEventListener('livewire:navigated', function (){
            const text_search = document.getElementsByClassName('text_search_{{ $id }}')[0];
            $('.li_{{ $id }} a').off("click");
            $('.li_{{ $id }} a').on("click", function (){
                const hiddenValueHolder =  document.getElementById('text_id_{{ $id }}');
                hiddenValueHolder.value = $(this).attr('data-value');
                hiddenValueHolder.dispatchEvent(new Event('input'));
                $('#dropdownMenuButton{{ $id }}').html($(this).attr('data-label'));
            });
            @php
                if(isset($ajax)){
            @endphp
            text_search.addEventListener('keyup', debounce(function (){
                if(text_search.value.length > 3) {
                    $.getJSON('{{ $ajax }}?s=' + text_search.value, function (success) {
                        document.getElementById('li_holder_{{ $id }}').innerHTML = "";
                        const data = success.data;
                        if (data.length > 0 && text_search.value.length > 0) {
                            let html = '';
                            data.forEach(function (item) {
                                html += '<li class="li_{{ $id }}"><a class="dropdown-item"  style="cursor: pointer; font-weight: bolder" data-value="' + item['id'] + '" data-label="' + (item['text'] ?? item['name']) + '">' + (item['text'] ?? item['name']) + '</a></li>'
                            })
                            document.getElementById('li_holder_{{ $id }}').innerHTML = html;
                            $('.li_{{ $id }} a').off("click");
                            $('.li_{{ $id }} a').on("click", function () {
                                const hiddenValueHolder = document.getElementById('text_id_{{ $id }}');
                                hiddenValueHolder.value = $(this).attr('data-value');
                                hiddenValueHolder.dispatchEvent(new Event('input'));
                                $('#dropdownMenuButton{{ $id }}').html($(this).attr('data-label'));
                            });
                        }

                    });
                } else {
                    document.getElementById('li_holder_{{ $id }}').innerHTML = "";
                }
            },500));
            @php
                }else{
            @endphp
            text_search.addEventListener('keyup', function (){
                const itemList = document.getElementsByClassName('li_{{ $id }}');
                for (i = 0; i < itemList.length; i++) {
                    a = itemList[i].getElementsByTagName("a")[0];
                    txtValue = a.textContent || a.innerText;
                    if (txtValue.toLowerCase().indexOf(text_search.value.toLowerCase()) > -1) {
                        itemList[i].style.display = "";
                    } else {
                        itemList[i].style.display = "none";
                    }
                }
            });

            const options = @json($options);
            options.forEach(function (item) {
                if(@this.get('{{ $attributes['wire:model'] ?? $attributes['wire:model.live'] }}') == item.id){
                    $('#dropdownMenuButton{{ $id }}').html(item.text ?? item.name);
                    @this.set('{{ $attributes['wire:model'] ?? $attributes['wire:model.live'] }}', item.id{{ isset($attributes['wire:model.live']) ? ",true" : "" }})
                    return;
                }
            });

            Livewire.hook('morph.updated', ({ el, component }) => {
                if (el === document.getElementById('text_id_{{ $id }}')) {
                    @php
                        $key = explode(".",($attributes['wire:model'] ?? $attributes['wire:model.live']));
                        $key = $key[count($key)-1];
                        if(empty($key)){
                            $key = $attributes['wire:model'];
                        }
                    @endphp
                    let seen = false;
                    if(component.$wire.get('{{ ($attributes['wire:model'] ?? $attributes['wire:model.live']) }}') !== undefined && component.$wire.get('{{ ($attributes['wire:model'] ?? $attributes['wire:model.live']) }}')!=="") {

                        const optionsData = component.$wire.get("data").hasOwnProperty('{{ $key }}') ? component.$wire.get("data").{{ $key }}.options : options;
                        optionsData.forEach(function (item) {
                            if (component.$wire.get('{{ ($attributes['wire:model'] ?? $attributes['wire:model.live']) }}') == item.id) {
                                $('#dropdownMenuButton{{ $id }}').html(item.text ?? item.name);
                                seen = true;
                                el.value = component.$wire.get('{{ $attributes['wire:model'] ?? $attributes['wire:model.live'] }}')
                                return;
                            }
                        })
                        if (seen === false) {
                            $('#dropdownMenuButton{{ $id }}').html('{{ $placeholder }}');
                            el.value = "";
                        }
                    }else{
                        $('#dropdownMenuButton{{ $id }}').html('{{ $placeholder }}');
                        el.value = "";
                    }
                }
            });

            Livewire.hook('morph.added',  ({ el, component }) => {
                $('.li_{{ $id }} a').off("click");
                $('.li_{{ $id }} a').on("click", function (){
                    const hiddenValueHolder =  document.getElementById('text_id_{{ $id }}');
                    hiddenValueHolder.value = $(this).attr('data-value');
                    hiddenValueHolder.dispatchEvent(new Event('input'));
                    $('#dropdownMenuButton{{ $id }}').html($(this).attr('data-label'));
                });
            })

            @php
                }
            @endphp
        });
    </script>
    <div class="dropdown dropdownMenuButton{{ $id }}">
        @if ($attributes->isNotEmpty())
            <input style="display: none" type="text"  {{ $attributes }}   id="text_id_{{ $id }}" />
        @else
            <input style="display: none"  type="text" wire:model="{{ $model }}" class="{{ $class }}"  id="text_id_{{ $id }}" />
        @endif
        <button  class="btn-select-toggle btn btn-phoenix-secondary form-control dropdown-toggle w-100 text-start"  type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            @if(isset($ajax))
                <span id="dropdownMenuButton{{ $id }}">{{ $value=="" ? $placeholder : $editModel::find($value)->{$editColumn} }}</span>
            @else
                <span wire:ignore id="dropdownMenuButton{{ $id }}">{{ isset($value) ? getCurrentLabel($options, $value) : $placeholder }}</span>
            @endif
        </button>
        <ul class="dropdown-menu w-100 dropdown-menu-end"  aria-labelledby="dropdownMenuButton">
            <div class="dropdown-item no-hover"><input placeholder="Search for item" type="text" class="form-control form-control-sm text_search_{{ $id }}"/></div>
            <div class="dropdown-divider"></div>
            <div id="li_holder_{{ $id }}" class="d-block" style="height : auto;max-height: 200px; overflow-y: scroll; width: 100%;">
                @if(!isset($ajax))
                    @foreach ((array) $options as $option)
                        @php
                            $option = (array)$option;
                        @endphp
                        <li class="li_{{ $id }}"><a class="dropdown-item"  style="cursor: pointer; font-weight: bolder" data-value="{{ $option['id'] }}" data-label="{{ $option['text'] ?? $option['name'] }}">{{ $option['text'] ?? $option['name'] }}</a></li>
                    @endforeach
                @endif
            </div>
        </ul>
    </div>



</div>

