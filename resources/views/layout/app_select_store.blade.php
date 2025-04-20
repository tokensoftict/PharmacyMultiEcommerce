@include('layout.base.base-1')
<main class="main" id="top">
    <livewire:layout.header />
    <div class="content">
    {{ $slot }}
    </div>
</main>
@include('layout.base.base-2')
