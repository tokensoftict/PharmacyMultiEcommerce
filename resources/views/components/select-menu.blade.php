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
    $actualId = $id . '-custom';
    $wireModelName = $attributes->wire('model')->value() ?: $model;
    $hasWireModel = !empty($wireModelName);
    $isLive = $live || ($attributes->wire('model') && $attributes->wire('model')->hasModifier('live'));
    
    // Normalize options into a standard array format: [['id' => '...', 'name' => '...']]
    $normalizedOptions = [];
    if (!empty($options)) {
        foreach ($options as $key => $opt) {
            if (is_array($opt) || is_object($opt)) {
                $opt = (array)$opt;
                $optId = $opt['id'] ?? $opt['value'] ?? $opt['code'] ?? $key;
                $optName = $opt['name'] ?? $opt['text'] ?? $opt['label'] ?? (string)$optId;
                $normalizedOptions[] = ['id' => (string)$optId, 'name' => (string)$optName];
            } else {
                $normalizedOptions[] = ['id' => (string)$key, 'name' => (string)$opt];
            }
        }
    }

    // Safely get initial value from wire:model or value prop
    $initialValue = ($attributes->wire('model') ? $attributes->wire('model')->value() : null) ?? $value;

    // Resolve initial label
    $initialLabel = $placeholder;
    if ($multiple) {
        $count = is_array($initialValue) ? count($initialValue) : 0;
        $initialLabel = $count > 0 ? "{$count} selected" : $placeholder;
    } elseif ($initialValue !== null && $initialValue !== '') {
        $matched = false;
        foreach ($normalizedOptions as $opt) {
            if ((string)$opt['id'] === (string)$initialValue) {
                $initialLabel = $opt['name'];
                $matched = true;
                break;
            }
        }
        if (!$matched && $editModel && class_exists($editModel)) {
            try {
                $record = $editModel::find($initialValue);
                if ($record) {
                    $initialLabel = $record->{$editColumn} ?? $placeholder;
                }
            } catch (\Throwable $e) {}
        }
    }
@endphp

<div x-data="{
    open: false,
    search: '',
    options: @js($normalizedOptions),
    selected: {{ $hasWireModel ? '$wire.entangle(\'' . $wireModelName . '\')' . ($isLive ? '.live' : '') : '@js($initialValue)' }},
    label: @js($initialLabel),
    placeholder: @js($placeholder),
    isAjax: @js(!empty($ajax)),
    ajaxUrl: @js($ajax),
    loading: false,
    multiple: @js((bool)$multiple),
    abortCtrl: null,

    init() {
        this.updateLabel();
        this.$watch('selected', () => {
            this.updateLabel();
        });
    },

    updateLabel() {
        if (this.multiple) {
            const count = Array.isArray(this.selected) ? this.selected.length : 0;
            this.label = count > 0 ? count + ' selected' : this.placeholder;
            return;
        }

        if (this.selected === null || this.selected === undefined || this.selected === '') {
            this.label = this.placeholder;
            return;
        }

        if (Array.isArray(this.options) && this.options.length > 0) {
            const found = this.options.find(o => String(o.id) === String(this.selected));
            if (found) {
                this.label = found.name;
                return;
            }
        }

        if (!this.label || this.label === this.placeholder) {
            this.label = 'Selected (#' + this.selected + ')';
        }
    },

    toggle() {
        this.open = !this.open;
        if (this.open) {
            this.$nextTick(() => {
                if (this.$refs.searchInput) {
                    this.$refs.searchInput.focus();
                }
            });
        }
    },

    select(option) {
        const optId = String(option.id);
        if (this.multiple) {
            if (!Array.isArray(this.selected)) this.selected = [];
            const idx = this.selected.findIndex(s => String(s) === optId);
            if (idx > -1) {
                this.selected.splice(idx, 1);
            } else {
                this.selected.push(option.id);
            }
            this.updateLabel();
        } else {
            this.selected = option.id;
            this.label = option.name;
            this.open = false;
            this.search = '';
        }
    },

    isSelected(id) {
        const optId = String(id);
        if (this.multiple) {
            return Array.isArray(this.selected) && this.selected.some(s => String(s) === optId);
        }
        return String(this.selected) === optId;
    },

    clear() {
        this.selected = this.multiple ? [] : null;
        this.label = this.placeholder;
        this.open = false;
        this.search = '';
    },

    fetchOptions() {
        if (!this.isAjax) return;
        const q = (this.search || '').trim();
        if (q.length < 1) {
            this.options = [];
            this.loading = false;
            return;
        }

        if (this.abortCtrl) {
            this.abortCtrl.abort();
        }
        this.abortCtrl = new AbortController();
        this.loading = true;

        const separator = this.ajaxUrl.includes('?') ? '&' : '?';
        const url = `${this.ajaxUrl}${separator}searchTerm=${encodeURIComponent(q)}&s=${encodeURIComponent(q)}`;

        fetch(url, { signal: this.abortCtrl.signal })
            .then(res => res.json())
            .then(data => {
                const list = Array.isArray(data) ? data : (data.data || []);
                this.options = (Array.isArray(list) ? list : []).map(item => ({
                    id: String(item.id !== undefined ? item.id : (item.value !== undefined ? item.value : item)),
                    name: String(item.name || item.text || item.label || item.id || '')
                }));
                this.loading = false;
            })
            .catch(err => {
                if (err.name !== 'AbortError') {
                    this.loading = false;
                }
            });
    },

    get displayList() {
        if (!Array.isArray(this.options)) return [];
        if (this.isAjax) return this.options;
        const q = (this.search || '').toLowerCase().trim();
        if (!q) return this.options;
        return this.options.filter(o => o.name.toLowerCase().includes(q));
    }
}" 
wire:ignore.self
class="dropdown {{ $class }}" 
id="{{ $actualId }}"
@click.away="open = false">
    
    <button 
        type="button"
        class="custom-select-button btn btn-phoenix-secondary form-control w-100 text-start d-flex justify-content-between align-items-center"
        @click="toggle"
        :aria-expanded="open"
    >
        <span x-text="label" class="text-truncate flex-grow-1 me-2"></span>
        <div class="d-flex align-items-center ms-auto">
            <span x-show="selected !== null && selected !== '' && (!Array.isArray(selected) || selected.length > 0)" 
                  @click.stop="clear" 
                  class="fa-solid fa-xmark fs-9 me-2 text-400 hover-text-danger transition-base" 
                  style="cursor: pointer; padding: 2px;"></span>
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
                @input.debounce.300ms="fetchOptions"
                type="text" 
                class="form-control form-control-sm" 
                placeholder="Type to search..."
                @click.stop
            >
        </div>
        
        <div class="overflow-auto border-top pt-2" style="max-height: 250px;">
            <div x-show="loading" class="text-center p-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                <div class="text-muted fs-10 mt-1">Searching...</div>
            </div>
            
            <template x-for="(option, index) in displayList" :key="option.id + '-' + index">
                <button 
                    type="button"
                    class="dropdown-item rounded-2 py-2 d-flex justify-content-between align-items-center"
                    :class="isSelected(option.id) ? 'active' : ''"
                    @click="select(option)"
                >
                    <span x-text="option.name"></span>
                    <span x-show="isSelected(option.id)" class="fas fa-check fs-11"></span>
                </button>
            </template>
            
            <div x-show="!loading && displayList.length === 0" class="text-center p-2 text-muted fs-9 italic">
                No results found
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    .dropdown-item.active {
        background-color: var(--phoenix-primary-bg-subtle, #e0f2fe) !important;
        color: var(--phoenix-primary-text-emphasis, #0369a1) !important;
    }
    .custom-select-button::after {
        display: none !important;
    }
    .transition-base {
        transition: all 0.2s ease-in-out;
    }
</style>
