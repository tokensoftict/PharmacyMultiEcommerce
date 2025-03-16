<?php

use App\Classes\Settings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layout.auth')]class extends Component
{
    public string $password = '';


    private Settings $settings;

    public function boot(Settings $settings)
    {
        $this->settings = $settings;
    }


    /**
     * Confirm the current user's password.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (!Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => "Your password has an issue, please check and try again"
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirect(
            session('url.intended', route("dashboard"))
        );
    }
?>

<div class="auth-form-box">
    <div class="text-center"><a class="d-flex flex-center text-decoration-none mb-4" href="{{ asset("") }}">
            <div class="d-flex align-items-center fw-bolder fs-3 d-inline-block">
                <img src="{{(isset($this->store['logo']) && $this->store['logo'] !== NULL) ? (is_string($this->store['logo']) ? asset('logo/'.$this->store['logo']) : $this->store['logo']->temporaryUrl()) : asset('logo/placholder.jpg') }}"
                     alt="phoenix" width="200"/>
            </div>
        </a>
        <h4 class="text-body-highlight">Confirm Your Password</h4>
        <p class="text-body-tertiary mb-5"> This is a secure area of the application. Please confirm your password
            before continuing.</p>
        <form class="d-flex align-items-center mb-5" wire:submit="confirmPassword">

            @if (session('status'))
                <div class="mt-3 mb-0 alert alert-warning">
                    {{ session('status') }}
                </div>
            @endif

            <input class="form-control flex-1" id="email" required wire:model="password" type="password"
                   placeholder="Password"/>

            <x-input-error :messages="$errors->get('password')" class="mt-2"/>

            <button class="btn btn-phoenix-primary ms-2">Confirm Password
                <span class="fas fa-chevron-right ms-2"></span>
            </button>

        </form>
    </div>
</div>


