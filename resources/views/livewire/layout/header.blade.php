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

<nav class="navbar navbar-top fixed-top navbar-expand-lg" id="navbarTop">
    <div class="navbar-logo">
        <button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTopCollapse" aria-controls="navbarTopCollapse" aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
        <a class="navbar-brand me-1 me-sm-3" href="{{ asset('') }}">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center"><img src="{{  isset($store['logo']) ? asset('logo/'.$store['logo'])  :  asset('logo/placholder.jpg') }}" alt="{{ getStoreSettings()->name }}" width="50" />
                    <p class="logo-text ms-2 d-none d-sm-block">PS GDC</p>
                </div>
            </div>
        </a>
    </div>
    <div class="collapse navbar-collapse navbar-top-collapse order-1 order-lg-0 justify-content-center" id="navbarTopCollapse">
        <ul class="navbar-nav navbar-nav-top" data-dropdown-on-hover="data-dropdown-on-hover">
            @if(!is_null(\App\Classes\ApplicationEnvironment::$id))
                {!! getUserMenu2(\App\Classes\ApplicationEnvironment::$id) !!}
            @endif
        </ul>
    </div>
    <ul class="navbar-nav navbar-nav-icons flex-row">
        <li class="nav-item">
            <div class="theme-control-toggle fa-icon-wait px-2"><input class="form-check-input ms-0 theme-control-toggle-input" type="checkbox" data-theme-control="phoenixTheme" value="dark" id="themeControlToggle" /><label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme"><span class="icon" data-feather="moon"></span></label><label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch theme"><span class="icon" data-feather="sun"></span></label></div>
        </li>


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
                    <div class="overflow-auto scrollbar" style="height: 7rem;">
                        <ul class="nav d-flex flex-column mb-2 pb-1">
                            <li class="nav-item"><a class="nav-link px-3" href="#!">
                                    <span class="me-2 text-body" data-feather="user"></span><span>Profile</span>
                                </a>
                            </li>
                            @if(auth()->user()->app_users()->count() > 1)
                                <li class="nav-item"><a class="nav-link px-3" href="{{ route('select-application') }}">
                                        <span class="me-2 text-body" data-feather="refresh-cw"></span>
                                        <span>Switch Application</span>
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item"><a class="nav-link px-3" href="{{ route("logout-web") }}">
                                    <span class="me-2 text-body" data-feather="log-out"></span><span>Log out</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </li>
    </ul>
</nav>











