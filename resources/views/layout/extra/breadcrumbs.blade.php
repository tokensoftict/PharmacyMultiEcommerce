<nav class="mb-2" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        @stack('breadcrumbs')
    </ol>
</nav>

<div class="row align-items-center justify-content-between g-3 mb-4">
    <div class="col-12 col-sm-auto col-xl-4">
        <h2 class="mb-0">@yield('pageHeaderTitle')</h2>
    </div>
    <div class="col-12 col-sm-auto col-xl-8">
        <div class="row">
            @yield('filterTable')
        </div>
    </div>
</div>

