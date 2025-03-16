<div>
    @if(!$currentFolder)
        @include('livewire-filemanager::partials.empty-application')
    @else
        <div class="w-full" x-data="{ uploading: false, progress: 0 }"
            x-on:livewire-upload-start="uploading = true"
            x-on:livewire-upload-finish="uploading = false"
            x-on:livewire-upload-error="uploading = false"
            x-on:livewire-upload-progress="progress = $event.detail.progress">
            <div class="w-full shadow-sm bg-white pt-4 border border-slate-300 sm:rounded dark:bg-slate-800">
                <div class="px-4 pb-4 sm:px-5 flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-slate-300">
                        {{ $currentFolder->name }}

                        <span class="px-2 text-gray-600 dark:text-slate-400">|</span>
                        <span class="text-gray-500 text-sm dark:text-slate-400">{{ $currentFolder->elements() }}{!! ((count($selectedFolders) + count($selectedFiles)) > 0 ? ' <span class="text-slate-700">(' . (count($selectedFolders) + count($selectedFiles)) . ' ' . trans_choice('livewire-filemanager::filemanager.selected', (count($selectedFolders) + count($selectedFiles))) . ')</span>' : '') !!}</span>
                    </h2>

                    <div>
                        <input type="file" wire:model.live="files" name="files" id="fileInput" multiple style="display: none;">

                        <button class="border rounded p-1.5 px-2 md:px-4 flex text-sm items-center space-x-4 bg-slate-100 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-300" @click="Livewire.dispatch('reset-media', { media_id: null })" onclick="document.getElementById('fileInput').click();">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                            </svg>

                            <span class="hidden md:flex">{{ __('livewire-filemanager::filemanager.add_a_file') }}</span>
                        </button>
                    </div>

                    <div class="flex space-x-4 items-center">
                        <div class="flex items-center space-x-2 max-h-[25px]">
                            @if((count($selectedFolders) + count($selectedFiles)) > 0)
                                <div>
                                    <button @click="Livewire.dispatch('reset-media', { media_id: null })" class="border rounded p-1.5 border-red-600 text-white bg-red-500" wire:click="deleteItems">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="mx-2 px-2 dark:text-slate-500">|</div>
                            @endif

                            @if ($this->currentFolder->id !== 1)
                                <div>
                                    <button class="border rounded p-1.5 border-slate-300 dark:border-slate-600 dark:text-slate-500" @click="Livewire.dispatch('reset-media', { media_id: null })" wire:click="navigateToParent">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="mx-2 px-2 dark:text-slate-500">|</div>
                            @endif

                            <div>
                                <button class="border rounded p-1.5 border-slate-300 dark:border-slate-600 dark:text-slate-500" @click="Livewire.dispatch('reset-media', { media_id: null })" wire:click="createNewFolder">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5v6m3-3H9m4.06-7.19l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                    </svg>
                                </button>
                            </div>

                            <input wire:model.live="search" @click="Livewire.dispatch('reset-media', { media_id: null })" class="rounded border border-slate-300 w-full py-2 px-3 zinc-500 leading-tight focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:mr-2 dark:bg-slate-700 dark:border-slate-600 dark:text-slate-500" type="search" placeholder="{{ __('livewire-filemanager::filemanager.search') }}...">
                        </div>
                    </div>
                </div>

                <div id="filemanager-area"
                    x-data="FilemanagerComponent()"
                    @mousedown="initiateDrawing($event)" @mousemove="draw($event)" @mouseup="stopDrawing()" @mouseleave="stopDrawing()"
                    class="border-t border-slate-300 shadow-inner overflow-x-hidden relative dark:border-slate-700" x-bind:class="dropingFile ? 'bg-blue-50 dark:bg-slate-900/90 border-dashed' : ''">
                    @if($search)
                        <div class="px-4 sm:px-5 py-1 bg-gray-100 border-b border-slate-300 text-sm">{{ (count($searchedFiles) + count($folders)) }} {{ trans_choice('livewire-filemanager::filemanager.search_results', count($searchedFiles) + count($folders)) }}</div>
                    @endif

                    <template x-if="drawnArea">
                        <div class="drawn-area absolute z-10 border border-blue-300 bg-blue-100/60 dark:bg-blue-100/10 dark:border-gray-500/70" :style="{
                            left: drawnArea.left + 'px',
                            top: drawnArea.top + 'px',
                            width: drawnArea.width + 'px',
                            height: drawnArea.height + 'px'
                        }"></div>
                    </template>

                    <div
                    id="folder-container"
                    x-on:drop="dropingFile = false"
                    x-on:drop.prevent="handleFileDrop($event)"
                    x-on:dragover.prevent="dropingFile = true"
                    x-on:dragleave.prevent="dropingFile = false"
                    x-on:dblclick.self="$wire.createNewFolder()"
                    class="p-2 pb-10 min-h-[500px] select-none overflow-y-auto flex relative flex-wrap content-start">
                        @if ($isCreatingNewFolder)
                            <div class="cursor-pointer mb-4 max-w-[137px] min-w-[137px] max-h-[137px] min-h-[137px] items-start p-2 mx-1 text-center">
                                <x-livewire-filemanager::icons.folder class="mx-auto w-16 h-16 mb-2" />

                                <input type="text" id="new-folder-name" wire:model="newFolderName" wire:keydown.enter="saveNewFolder" class="text-center w-full rounded py-0.5 px-1 text-sm dark:bg-slate-800 dark:text-slate-200">
                            </div>
                        @endif

                        @foreach($folders->sortBy('name') as $folder)
                            <x-livewire-filemanager::elements.directory :folder="$folder" :selectedFolders="$selectedFolders" />
                        @endforeach

                        @if($searchedFiles)
                            @foreach($searchedFiles->sortBy('file_name') as $media)
                                <x-livewire-filemanager::elements.media :media="$media" :selectedFiles="$selectedFiles" />
                            @endforeach
                        @else
                            @foreach($currentFolder->getMedia('medialibrary')->sortBy('file_name') as $media)
                                <x-livewire-filemanager::elements.media :media="$media" :selectedFiles="$selectedFiles" />
                            @endforeach
                        @endif
                    </div>

                    <div class="w-full absolute left-0 right-0 p-1 border-l-0 border-r-0 border -bottom-1" x-cloak x-show="uploading">
                        <div class="w-full flex mb-1">
                            <progress class="w-full" max="100" x-bind:value="progress"></progress>
                        </div>
                    </div>

                    <livewire:livewire-filemanager.media-panel />
                    <livewire:livewire-filemanager.delete-items />
                </div>

                <nav class="select-none border-t text-sm px-4 sm:px-4 py-1.5 flex items-center border-slate-300 dark:border-slate-700 text-black dark:text-slate-300">
                    @foreach ($breadcrumb as $index => $folder)
                        <span class="cursor-pointer flex space-x-1 items-center" @click="Livewire.dispatch('reset-media', { media_id: null })" wire:click.prevent="navigateToBreadcrumb({{ $index }})">
                            <x-livewire-filemanager::icons.folder class="w-5 h-5" /> <span>{{ $folder->name }}</span>
                        </span>

                        @if (!$loop->last)
                            <div class="px-2">
                                <x-livewire-filemanager::icons.chevron />
                            </div>
                        @endif
                    @endforeach
                </nav>
            </div>
        </div>
    @endif

    <script>
        function FilemanagerComponent() {
            return {
                dropingFile: false,
                isDrawing: false,
                isPending: false,
                startX: 0,
                startY: 0,
                drawnArea: null,
                drawingTimeout: null,

                initiateDrawing(event) {
                    this.isPending = true;
                    const container = event.currentTarget;
                    const rect = container.getBoundingClientRect();

                    this.startX = event.clientX - rect.left;
                    this.startY = event.clientY - rect.top;

                    this.drawingTimeout = setTimeout(() => {
                        if (this.isPending) {
                            this.startDrawing();
                        }
                    }, 150); // Delay in milliseconds
                },

                startDrawing(event) {
                    this.isDrawing = true;
                    this.isPending = false;
                    this.drawnArea = {
                        left: this.startX,
                        top: this.startY,
                        width: 0,
                        height: 0
                    };
                },

                draw(event) {
                    if (!this.isDrawing) return;

                    const container = event.currentTarget;
                    const rect = container.getBoundingClientRect();

                    const currentX = event.clientX - rect.left;
                    const currentY = event.clientY - rect.top;

                    const width = currentX - this.startX;
                    const height = currentY - this.startY;

                    this.drawnArea.width = Math.abs(width);
                    this.drawnArea.height = Math.abs(height);

                    if (width < 0) {
                        this.drawnArea.left = currentX;
                    }

                    if (height < 0) {
                        this.drawnArea.top = currentY;
                    }
                },

                stopDrawing() {
                    if (this.isPending) {
                        clearTimeout(this.drawingTimeout);
                        this.isPending = false;
                    }

                    if (this.isDrawing) {
                        this.selectElementsWithinDrawnArea();

                        this.isDrawing = false;
                        this.drawnArea = null;
                    }
                },

                selectElementsWithinDrawnArea() {
                    const container = document.getElementById('folder-container');
                    const drawnRect = {
                        left: this.drawnArea.left,
                        top: this.drawnArea.top,
                        right: this.drawnArea.left + this.drawnArea.width,
                        bottom: this.drawnArea.top + this.drawnArea.height
                    };

                    container.querySelectorAll('.folder, .file').forEach(element => {
                        const rect = element.getBoundingClientRect();
                        const elementRect = {
                            left: rect.left - container.getBoundingClientRect().left,
                            top: rect.top - container.getBoundingClientRect().top,
                            right: rect.right - container.getBoundingClientRect().left,
                            bottom: rect.bottom - container.getBoundingClientRect().top
                        };

                        if (this.isElementWithinDrawnArea(drawnRect, elementRect)) {
                            const id = element.getAttribute('data-id');
                            const type = element.classList.contains('folder') ? 'folder' : 'file';

                            if (type == 'folder') {
                                this.$wire.toggleFolderSelection(id);
                            } else {
                                this.$wire.toggleFileSelection(id);
                            }
                        }
                    });
                },

                isElementWithinDrawnArea(drawnRect, elementRect) {
                    return !(drawnRect.left > elementRect.right ||
                             drawnRect.right < elementRect.left ||
                             drawnRect.top > elementRect.bottom ||
                             drawnRect.bottom < elementRect.top);
                },

                handleFileDrop(e) {
                    if (event.dataTransfer.files.length > 0) {
                        const files = e.dataTransfer.files;
                        @this.uploadMultiple('files', files,
                            (uploadedFilename) => {}, () => {}, (event) => {}
                        )
                    }
                }
            };
        }

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('new-folder-created', function () {
                const checkExist = setInterval(function() {
                    let input = document.getElementById('new-folder-name');
                    input.focus();
                    input.select();

                    clearInterval(checkExist);
                }, 100);
            });

            Livewire.on('copy-link', function (event) {
                navigator.clipboard.writeText(event.link)
                .then(() => {
                })
                .catch(err => {
                    console.error('Error in copying text: ', err);
                });
            });
        });
    </script>
</div>
