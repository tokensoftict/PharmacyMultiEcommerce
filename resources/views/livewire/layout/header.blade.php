<?php

use Livewire\Attributes\On;
use Livewire\Volt\Component;
use App\Classes\Settings;

new class extends Component
{
    private Settings $settings;
    public array $store;

    public function boot(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function mount()
    {
        $this->store = $this->settings->all();
    }
}
?>

<nav class="navbar navbar-top fixed-top navbar-expand" id="navbarDefault">
    <div class="collapse navbar-collapse justify-content-between">
        <div class="navbar-logo">
            <button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
            <a class="navbar-brand me-1 me-sm-3" href="{{ asset('') }}">
                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center"><img src="{{  isset($store['logo']) ? asset('logo/'.$store['logo'])  :  asset('logo/placholder.jpg') }}" alt="phoenix" width="80" />
                        <p class="logo-text ms-2 d-none d-sm-block"></p>
                    </div>
                </div>
            </a>
        </div>

        <ul class="navbar-nav navbar-nav-icons flex-row">

            <li class="nav-item dropdown"><a class="nav-link lh-1 pe-0" id="navbarDropdownUser" href="#!" role="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                    <div class="avatar avatar-l ">
                        <img class="rounded-circle " src="{{ asset(auth()->user()->image) }}" alt="" />
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end navbar-dropdown-caret py-0 dropdown-profile shadow border" aria-labelledby="navbarDropdownUser">
                    <div class="card position-relative border-0">
                        <div class="card-body p-0">
                            <div class="text-center pt-4 pb-3">
                                <div class="avatar avatar-xl ">
                                    <img class="rounded-circle " src="{{ asset(auth()->user()->image) }}" alt="" />
                                </div>
                                <h6 class="mt-2 text-body-emphasis">{{ auth()->user()->name }}</h6>
                            </div>
                        </div>
                        <div class="overflow-auto scrollbar">
                            <ul class="nav d-flex flex-column mb-2 pb-1">
                                <li class="nav-item"><a class="nav-link px-3" href="#!">
                                        <span class="me-2 text-body" data-feather="user"></span>
                                        <span>Profile</span>
                                    </a>
                                </li>
                                @if(auth()->user()->app_users()->count() > 1)
                                    <li class="nav-item"><a class="nav-link px-3" href="{{ route('select-application') }}">
                                            <span class="me-2 text-body" data-feather="refresh-cw"></span>
                                            <span>Switch Application</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                        <div class="card-footer p-0 border-top border-translucent">
                            <div class="px-3"> <a class="btn btn-phoenix-secondary d-flex flex-center mt-3 w-100" href="{{ route("logout-web") }}">
                                    <span class="me-2" data-feather="log-out"> </span>Sign out</a>
                            </div>
                            <div class="my-2 text-center fw-bold fs-10 text-body-quaternary">
                                <a class="text-body-quaternary me-1" href="#!">Privacy policy</a>&bull;
                                <a class="text-body-quaternary mx-1" href="#!">Terms</a>&bull;
                                <a class="text-body-quaternary ms-1" href="#!">Cookies</a>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</nav>
