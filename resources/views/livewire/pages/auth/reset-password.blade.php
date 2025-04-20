<?php

use App\Classes\Settings;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;
use \Illuminate\Support\Facades\Lang;

new #[Layout('layout.auth')] class extends Component {
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';


    private Settings $settings;

    public function boot(Settings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;

        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', 'min:6'],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => bcrypt($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', Lang::get($status));

            return;
        }

        Session::flash('status', Lang::get($status));

        $this->redirectRoute('login');
    }
}; ?>



<div class="text-center mb-6">
    <h4 class="text-body-highlight">Reset new password</h4>
    <p class="text-body-tertiary">Type your new password</p>
    <form class="mt-5" wire:submit="resetPassword">
        <input class="form-control mb-2" wire:model="email" id="email" type="email" required autofocus
               autocomplete="username" placeholder="Email"/>
        @error('email')
        <span class="d-block text-danger small mb-2">{{ $message }}</span>
        @enderror

        <div class="position-relative mb-2" data-password="data-password">
            <input class="form-control form-icon-input pe-6" wire:model="password" id="password" type="password" placeholder="Type new password" data-password-input="data-password-input" />
        </div>
        @error('password')
            <span class="d-block text-danger small mb-2">{{ $message }}</span>
        @enderror

        <div class="position-relative mb-4" data-password="data-password">
            <input class="form-control form-icon-input pe-6" wire:model="password_confirmation" id="confirmPassword" type="password" placeholder="Confirm new password" data-password-input="data-password-input" />
        </div>
        @error('password_confirmation')
            <span class="d-block text-danger small mb-2">{{ $message }}</span>
        @enderror

        <button class="btn btn-danger w-100" type="submit">Set Password</button>
        <br/><br/>
       <p> <a class="fs-9 fw-bold" href="{{ route("login") }}">Back to Login</a></p>
    </form>
</div>




