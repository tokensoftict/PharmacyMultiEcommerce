@include('layout.base.base-1')
<main class="main bg-white" id="top">
    <div class="container">
        <div class="row flex-center min-vh-100 py-5">
            <div class="col-sm-10 col-md-8 col-lg-5 col-xxl-4">
                <a class="d-flex flex-center text-decoration-none mb-4" href="{{ route('customer.index') }}">
                    <div class="d-flex align-items-center fw-bolder fs-3 d-inline-block">
                        <img src="{{ asset("logo/placholder.jpg") }}" alt="phoenix" width="100" />
                    </div>
                </a>
                {{ $slot }}
            </div>
        </div>
    </div>
</main>

@include('layout.base.base-2')


