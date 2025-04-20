<?php

use App\Classes\Settings;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layout.auth')] class extends Component {
    public string $email = '';


    private Settings $settings;

    public function boot(Settings $settings)
    {
        $this->settings = $settings;
    }

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
            $this->addError('email', __($status));
            session()->flash('error', __($status));
            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}
?>

<div class="px-xxl-5">
    <div class="text-center mb-6">
        <h4 class="text-body-highlight">Forgot your password?</h4>
        <p class="text-body-tertiary mb-5">Enter your email below and we will send <br class="d-sm-none" />you a reset link</p>
        @if(session()->has('status'))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-success alert-sma alert-dismissible fade show" role="alert">
                        <strong>{{ session('status') }}.</strong>
                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif
        @if(session()->has('error'))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger alert-sma alert-dismissible fade show" role="alert">
                        <strong>{{ session('error') }}.</strong>
                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif
        <form class="d-flex align-items-center mb-5" wire:submit="sendPasswordResetLink">
            <input class="form-control flex-1" id="email"  wire:model="email" type="email"  placeholder="Email Address" />
            <button class="btn btn-danger ms-2" type="submit">
                Send <span class="fas fa-chevron-right ms-2"></span>
            </button>
        </form>
        <a class="fs-9 fw-bold" href="{{ route("login") }}">Back to Login</a>
    </div>
</div>





