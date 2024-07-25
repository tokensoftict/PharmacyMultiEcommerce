<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use \App\Notifications\NewAccountRegistration;

new #[Layout('layout.auth')] class extends Component
{
    /**
     * Send an email verification notification to the user.
     */
    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirect(
                session('url.intended', route("login")),
                navigate: true
            );

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        Session::flash('status', \Illuminate\Support\Facades\Lang::get('verification-link-sent'));
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/');
    }
}
?>

<div class="auth-form-box">
    <p class="text-center mb-6 mx-auto"><img class="mb-7 d-dark-none" src="{{ asset("images/icons/illustrations/11.png") }}" alt="phoenix" /><img class="mb-7 d-light-none" src="{{ asset("images/spot-illustrations/dark_1.png") }}" alt="{{ config("app.name") }}" />
    <div class="mb-6">
        <h4 class="text-body-highlight">Account Verification</h4>
        <p class="text-body-tertiary">Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <p class="text-body-tertiary text-success">
            A new verification link has been sent to the email address you provided during registration.
        </p>
    @endif

    <div class="d-grid">
        <a class="btn btn-phoenix-success" href="#" wire:click="sendVerification"><span class="fas fa-refresh me-2"></span>Resend Verification</a>

        <a class="btn btn-phoenix-primary" href="#" wire:click="logout"><span class="fas fa-lock me-2"></span>Log out</a>
    </div>
</div>



