@include('layout.base.base-1')

<div class="container-fluid bg-body-tertiary dark__bg-gray-1200">
    <div class="bg-holder bg-auth-card-overlay" style="background-image:url({{ asset('images/bg/37.png') }});"></div>

    <div class="row flex-center position-relative min-vh-100 g-0 py-5">
        <div class="col-11 col-sm-10 col-xl-8">
            <div class="card border border-translucent auth-card">
                <div class="card-body pe-md-0">
                    <div class="row align-items-center gx-0 gy-7">
                        <div class="col-auto bg-body-highlight dark__bg-gray-1100 rounded-3 position-relative overflow-hidden auth-title-box">
                            <div class="bg-holder" style="background-image:url({{ asset('images/bg/38.png') }});"></div>
                            <!--/.bg-holder-->
                            <div class="position-relative px-4 px-lg-7 pt-7 pb-7 pb-sm-5 text-center text-md-start pb-lg-7 pb-md-7">
                                <h3 class="mb-3 text-body-emphasis fs-7">{{ config('app.name') }}</h3>
                                <p class="text-body-tertiary">Manage your pharmacy effortlessly! Track inventory, process orders, update product details, and monitor sales, all from one secure and user-friendly dashboard.</p>
                                <ul class="list-unstyled mb-0 w-max-content w-md-auto">
                                    <li class="d-flex align-items-center"><span class="uil uil-check-circle text-success me-2"></span><span class="text-body-tertiary fw-semibold">Manage Customers</span></li>
                                    <li class="d-flex align-items-center"><span class="uil uil-check-circle text-success me-2"></span><span class="text-body-tertiary fw-semibold">Manage Orders</span></li>
                                    <li class="d-flex align-items-center"><span class="uil uil-check-circle text-success me-2"></span><span class="text-body-tertiary fw-semibold">Manage Sales Representatives</span></li>
                                </ul>
                            </div>
                            <div class="position-relative z-n1 mb-6 d-none d-md-block text-center mt-md-15">
                                <img class="auth-title-box-img d-dark-none" src="{{ asset("images/spot-illustrations/auth.png") }}" alt="" />
                                <img class="auth-title-box-img d-light-none" src="{{ asset("images/spot-illustrations/auth-dark.png") }}" alt="" />
                            </div>
                        </div>

                        <div class="col mx-auto">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@include('layout.base.base-2')


