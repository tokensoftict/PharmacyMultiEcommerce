<?php

use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\RedirectResponse;

new #[Layout('layout.auth')] class extends Component
{
    public LoginForm $form;

    public string $password;

    public string $user_name;

    public bool $remember;

    /**
     * Handle an incoming authentication request.
     */
    public function login() : void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirect("select-application");
    }
}
?>


<div class="auth-form-box">
    <form wire:submit="login">
        <div class="text-center mb-7">
            <a class="d-flex flex-center text-decoration-none mb-4" href="#">
                <div class="d-flex align-items-center fw-bolder fs-3 d-inline-block">
                    <img src="{{ asset("images/logo.png") }}" alt="phoenix" width="58" />
                </div>
            </a>
            <h3 class="text-body-highlight">Sign In</h3>
            <p class="text-body-tertiary">Get access to your account</p>
        </div>
        <button class="btn btn-phoenix-secondary w-100 mb-3">
            <span class="fab fa-google text-danger me-2 fs-9"></span>
            Sign in with google
        </button>
        <button class="btn btn-phoenix-secondary w-100">
            <span class="fab fa-facebook text-primary me-2 fs-9"></span>
            Sign in with facebook</button>
        <div class="position-relative">
            <hr class="bg-body-secondary mt-5 mb-4" />
            <div class="divider-content-center bg-body-emphasis">or use email</div>
        </div>
        <div class="mb-3 text-start">
            <label class="form-label" for="email">Email address</label>
            <div class="form-icon-container">
                <input class="form-control form-icon-input" wire:model="form.email" id="email" placeholder="name@example.com"  autofocus
                       autocomplete="email" />
                <span class="fas fa-user text-body fs-9 form-icon"></span>
            </div>
            @error('form.email')
            <span class="d-block text-danger small">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3 text-start">
            <label class="form-label" for="password">Password</label>
            <div class="form-icon-container">
                <input class="form-control form-icon-input" wire:model="form.password" id="password" type="password" placeholder="Password" />
                <span class="fas fa-key text-body fs-9 form-icon"></span>
            </div>
            @error('form.password')
            <span class="d-block text-danger small">{{ $message }}</span>
            @enderror
        </div>
        <div class="row flex-between-center mb-7">
            <div class="col-auto">
                <div class="form-check mb-0">
                    <input class="form-check-input" wire:model="form.remember" id="basic-checkbox" type="checkbox" checked="checked" />
                    <label class="form-check-label mb-0" for="basic-checkbox">Remember me</label>
                </div>
            </div>
            <div class="col-auto"><a class="fs-9 fw-semibold" href="{{ route("password.request") }}">Forgot Password?</a></div>
        </div>
        <button class="btn btn-phoenix-primary w-100 mb-3">
            Sign In
        </button>
        <div class="text-center">
            <a class="fs-9 fw-bold" href="{{ route("register") }}">Create an account</a>
        </div>
    </form>
</div>



