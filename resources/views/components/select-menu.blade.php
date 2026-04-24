@props([
    'options' => [],
    'model' => null,
    'id' => null,
    'placeholder' => 'Select Item',
    'value' => null,
    'ajax' => null,
    'editModel' => null,
    'editColumn' => 'name',
    'class' => '',
    'live' => false,
    'multiple' => false
])

@php
    $id = $id ?? 'select-' . Str::random(8);
    // Suffix the ID to prevent legacy scripts (Select2) from targeting this component
    $actualId = $id . '-custom';
    $isLive = $live || ($attributes->wire('model') && $attributes->wire('model')->hasModifier('live'));
    $optionsHash = md5(json_encode($options));

    $initialLabel = $placeholder;
    if ($multiple) {
        $initialLabel = '0 selected';
        if ($initialValue && is_array($initialValue) && count($initialValue) > 0) {
            $initialLabel = count($initialValue) . ' selected';
        }
    } elseif ($initialValue) {
        if (!empty($options)) {
            foreach ($options as $option) {
                $option = (array)$option;
                if ($option['id'] == $initialValue) {
                    $initialLabel = $option['text'] ?? $option['name'] ?? $placeholder;
                    break;
                }
            }
        } elseif ($editModel && class_exists($editModel)) {
            try {
                $record = $editModel::find($initialValue);
                if ($record) {
                    $initialLabel = $record->{$editColumn} ?? $placeholder;
                }
            } catch (\Exception $e) {}
        }
    }
@endphp

<div x-data="{
    open: false,
    search: '',
    options: @js((array)$options),
    selected: @entangle($attributes->wire('model')){{ $isLive ? '.live' : '' }},
    label: @js($initialLabel),
    placeholder: @js($placeholder),
    isAjax: @js(!empty($ajax)),
    ajaxUrl: @js($ajax),
    loading: false,
    multiple: @js($multiple),

    init() {
        this.$watch('selected', value => {
            this.updateLabel();
        });
    },

    updateLabel() {
        if (this.multiple) {
            if (!Array.isArray(this.selected) || this.selected.length === 0) {
                this.label = '0 selected';
            } else {
                this.label = this.selected.length + ' selected';
            }
            return;
        }

        if (!this.selected) {
            this.label = this.placeholder;
            return;
        }

        if (!this.isAjax) {
            const found = this.options.find(o => o.id == this.selected);
            if (found) {
                this.label = found.text || found.name;
            }
        }
    },

    toggle() {
        this.open = !this.open;
        if (this.open) {
            this.$nextTick(() => { this.$refs.searchInput.focus() });
        }
    },

    select(option) {
        if (this.multiple) {
            if (!Array.isArray(this.selected)) this.selected = [];
            const index = this.selected.indexOf(option.id);
            if (index > -1) {
                this.selected.splice(index, 1);
            } else {
                this.selected.push(option.id);
            }
            this.updateLabel();
            this.$dispatch('input', this.selected);
        } else {
            this.selected = option.id;
            this.label = option.text || option.name;
            this.open = false;
            this.search = '';
            this.$dispatch('input', option.id);
        }
    },

    isSelected(id) {
        if (this.multiple) {
            return Array.isArray(this.selected) && this.selected.includes(id);
        }
        return this.selected == id;
    },

    clear() {
        this.selected = this.multiple ? [] : null;
        this.updateLabel();
        this.open = false;
        this.search = '';
        this.$dispatch('input', this.selected);
    },

    fetchOptions() {
        if (!this.isAjax || this.search.length < 2) return;
        this.loading = true;
        fetch(`${this.ajaxUrl}${this.ajaxUrl.includes('?') ? '&' : '?'}s=${this.search}`)
            .then(res => res.json())
            .then(data => {
                this.options = data.data || data;
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
            });
    },

    filteredOptions() {
        if (this.isAjax) return this.options;
        return this.options.filter(o => {
            const text = (o.text || o.name || '').toLowerCase();
            return text.includes(this.search.toLowerCase());
        });
    }
}" 
x-effect="options = @js((array)$options)"
class="dropdown {{ $class }}" 
id="{{ $actualId }}"
wire:key="{{ $actualId }}-{{ $optionsHash }}"
@click.away="open = false">
    
    <button 
        type="button"
        class="custom-select-button btn btn-phoenix-secondary form-control w-100 text-start d-flex justify-content-between align-items-center"
        @click="toggle"
        :aria-expanded="open"
    >
        <span x-text="label" class="text-truncate flex-grow-1 me-2"></span>
        <div class="d-flex align-items-center ms-auto">
            <span x-show="selected" @click.stop="clear" class="fa-solid fa-xmark fs-9 me-2 text-400 hover-text-danger transition-base" style="cursor: pointer; padding: 2px;"></span>
            <span class="fas fa-chevron-down fs-10 text-400"></span>
        </div>
    </button>

    <div 
        x-show="open"
        x-transition
        class="dropdown-menu show w-100 shadow-sm p-2"
        style="margin-top: 2px; position: absolute; z-index: 1050;"
        x-cloak
    >
        <div class="mb-2">
            <input 
                x-ref="searchInput"
                x-model="search"
                @keyup.debounce.500ms="fetchOptions"
                type="text" 
                class="form-control form-control-sm" 
                placeholder="Type to search..."
                @click.stop
            >
        </div>
        
        <div class="overflow-auto border-top pt-2" style="max-height: 250px;">
            <template x-if="loading">
                <div class="text-center p-3">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                </div>
            </template>
            
            <template x-for="option in filteredOptions()" :key="option.id">
                <button 
                    type="button"
                    class="dropdown-item rounded-2 py-2 d-flex justify-content-between align-items-center"
                    :class="isSelected(option.id) ? 'active' : ''"
                    @click="select(option)"
                >
                    <span x-text="option.text || option.name"></span>
                    <span x-show="isSelected(option.id)" class="fas fa-check fs-11"></span>
                </button>
            </template>
            
            <template x-if="filteredOptions().length === 0 && !loading">
                <div class="text-center p-2 text-muted fs-9 italic">No results found</div>
            </template>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    .dropdown-item.active {
        background-color: var(--phoenix-primary-bg-subtle);
        color: var(--phoenix-primary-text-emphasis);
    }
    .custom-select-button::after {
        display: none !important;
    }
    .transition-base {
        transition: all 0.2s ease-in-out;
    }
</style>
