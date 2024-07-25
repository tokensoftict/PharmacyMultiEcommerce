@php($tableName = $this->getTableName())

<div>
    <div class="mx-n4 mx-lg-n6 px-4 px-lg-6 mb-9 bg-body-emphasis border-y mt-2 position-relative top-1 pt-4 pb-4">

        <x-livewire-tables::wrapper :component="$this" :tableName="$tableName">
            @if ($this->hasConfigurableAreaFor('before-tools'))
                @include($this->getConfigurableAreaFor('before-tools'), $this->getParametersForConfigurableArea('before-tools'))
            @endif

            <x-livewire-tables::tools>
                <x-livewire-tables::tools.sorting-pills />
                <x-livewire-tables::tools.filter-pills />
                <x-livewire-tables::tools.toolbar :$filterGenericData />
            </x-livewire-tables::tools>

            <x-livewire-tables::table>
                <x-slot name="thead">
                    <x-livewire-tables::table.th.reorder x-cloak x-show="currentlyReorderingStatus" />
                    <x-livewire-tables::table.th.bulk-actions :displayMinimisedOnReorder="true" />
                    <x-livewire-tables::table.th.collapsed-columns />

                    @foreach($columns as $index => $column)
                        @continue($column->isHidden())
                        @continue($this->columnSelectIsEnabled() && ! $this->columnSelectIsEnabledForColumn($column))
                        @continue($column->isReorderColumn() && !$this->getCurrentlyReorderingStatus() && $this->getHideReorderColumnUnlessReorderingStatus())

                        <x-livewire-tables::table.th wire:key="{{ $tableName.'-table-head-'.$index }}" :column="$column" :index="$index" />
                    @endforeach
                </x-slot>

                @if($this->secondaryHeaderIsEnabled() && $this->hasColumnsWithSecondaryHeader())
                    <x-livewire-tables::table.tr.secondary-header :rows="$rows" :$filterGenericData />
                @endif
                @if($this->hasDisplayLoadingPlaceholder())
                    <x-livewire-tables::includes.loading colCount="{{ $this->columns->count()+1 }}" />
                @endif


                <x-livewire-tables::table.tr.bulk-actions :rows="$rows" :displayMinimisedOnReorder="true" />

                @forelse ($rows as $rowIndex => $row)
                    <x-livewire-tables::table.tr wire:key="{{ $tableName }}-row-wrap-{{ $row->{$this->getPrimaryKey()} }}" :row="$row" :rowIndex="$rowIndex">
                        <x-livewire-tables::table.td.reorder x-cloak x-show="currentlyReorderingStatus" wire:key="{{ $tableName }}-row-reorder-{{ $row->{$this->getPrimaryKey()} }}" :rowID="$tableName.'-'.$row->{$this->getPrimaryKey()}" :rowIndex="$rowIndex" />
                        <x-livewire-tables::table.td.bulk-actions wire:key="{{ $tableName }}-row-bulk-act-{{ $row->{$this->getPrimaryKey()} }}" :row="$row" :rowIndex="$rowIndex"/>
                        <x-livewire-tables::table.td.collapsed-columns wire:key="{{ $tableName }}-row-collapsed-{{ $row->{$this->getPrimaryKey()} }}" :rowIndex="$rowIndex" />

                        @foreach($columns as $colIndex => $column)
                            @continue($column->isHidden())
                            @continue($this->columnSelectIsEnabled() && ! $this->columnSelectIsEnabledForColumn($column))
                            @continue($column->isReorderColumn() && !$this->getCurrentlyReorderingStatus() && $this->getHideReorderColumnUnlessReorderingStatus())

                            <x-livewire-tables::table.td wire:key="{{ $tableName . '-' . $row->{$this->getPrimaryKey()} . '-datatable-td-' . $column->getSlug() }}"  :column="$column" :colIndex="$colIndex">
                                {{ $column->renderContents($row) }}
                            </x-livewire-tables::table.td>
                        @endforeach
                    </x-livewire-tables::table.tr>

                    <x-livewire-tables::table.collapsed-columns :row="$row" :rowIndex="$rowIndex" />
                @empty
                    <x-livewire-tables::table.empty />
                @endforelse

                @if ($this->footerIsEnabled() && $this->hasColumnsWithFooter())
                    <x-slot name="tfoot">
                        @if ($this->useHeaderAsFooterIsEnabled())
                            <x-livewire-tables::table.tr.secondary-header :rows="$rows" :$filterGenericData />
                        @else
                            <x-livewire-tables::table.tr.footer :rows="$rows"  :$filterGenericData />
                        @endif
                    </x-slot>
                @endif
            </x-livewire-tables::table>

            <x-livewire-tables::pagination :rows="$rows" />
        </x-livewire-tables::wrapper>
    </div>


    @includeIf($customView)
    @if(isset($this->extraRowActionButton))
        @foreach($this->extraRowActionButton as $index=>$component)
            @if($component['type'] === 'component' && userCanView($component['permission']))
                @livewire($component['component'], key(str_replace("\\","",$index)))
            @endif
        @endforeach
    @endif
    @if(isset($this->toolbarButtons))
        @foreach($this->toolbarButtons as $key => $toolbarButton)
            @if($toolbarButton['type'] === 'component' && userCanView($toolbarButton['permission']))
                @livewire($toolbarButton['component'], key(str_replace("\\","",$index)))
            @endif
        @endforeach
    @endif
</div>



@script
<script>
    let formData = {};
    document.addEventListener('livewire:navigated',function(){
        Livewire.hook('morph.updated', ({ el, component }) => {
            if(typeof component?.snapshot?.data?.data === "undefined"){
                return;
            }
            formData = component?.snapshot?.data?.data[0] ?? formData;
            for (let [key, value] of Object.entries(formData)) {
                value = value[0];
                if(el === document.getElementById('select'+key))
                {
                    if($('#select'+key).data('select2')) {
                        $('#select'+key).select2('destroy');
                    }

                    $('#select'+key).select2({
                        dropdownParent: $('#modal-holder'),
                        placeholder:  value['placeholder'] ?? "Select"+" "+value['label'],
                        searchInputPlaceholder: value['placeholder'] ?? "Select"+" "+value['label'],
                    });

                    $('#select'+key).val(component.$wire.get('formData.'+key));
                    $('#select'+key).off('change');
                    $('#select'+key).on('change', function (e) {
                        component.$wire.set('formData.'+key, $(this).val());
                    });
                }
            }

        })
    });
</script>
@endscript

