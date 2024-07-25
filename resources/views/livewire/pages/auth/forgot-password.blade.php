<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layout.auth')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', $status);

            return;
        }

        $this->reset('email');

        session()->flash('status', $status);
    }
}
?>

<div class="auth-form-box">
    <div class="text-center"><a class="d-flex flex-center text-decoration-none mb-4" href="{{ asset("") }}">
            <div class="d-flex align-items-center fw-bolder fs-3 d-inline-block">
                <img src="{{ asset("images/logo.png") }}" alt="logo" width="58" />
            </div>
        </a>
        <h4 class="text-body-highlight">Forgot your password?</h4>
        <p class="text-body-tertiary mb-5">Enter your email below and we will <br class="d-md-none" />send you <br class="d-none d-xxl-block" />a reset link</p>
        <form class="d-flex align-items-center mb-5">
            @if (session('status'))
                <div class="mt-3 mb-0 alert alert-warning">
                    {{ session('status') }}
                </div>
            @endif
            <input class="form-control flex-1" id="email" wire:model="email" type="email" placeholder="Email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            <button class="btn btn-phoenix-primary ms-2">Send<span class="fas fa-chevron-right ms-2"></span></button>
        </form>
        <a class="fs-9 fw-bold" href="{{ route("login") }}">Back to Login</a>
    </div>
</div>


