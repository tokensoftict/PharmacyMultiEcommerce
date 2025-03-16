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

<div>

    <div class="auth-form-box">
        <div class="text-center mb-7">
            <a class="d-flex flex-center text-decoration-none mb-4" href="{{ asset('') }}">
                <div class="d-flex align-items-center fw-bolder fs-3 d-inline-block">
                    <img src="{{(isset($this->store['logo']) && $this->store['logo'] !== NULL) ? (is_string($this->store['logo']) ? asset('logo/'.$this->store['logo']) : $this->store['logo']->temporaryUrl()) : asset('logo/placholder.jpg') }}"
                         alt="phoenix" width="200"/>
                </div>
            </a>
            <h4 class="text-body-highlight">Reset Password</h4>
            <p class="text-body-tertiary">Type your new password</p>
        </div>
        <form class="mt-5" wire:submit="resetPassword">
            <input class="form-control mb-2" wire:model="email" id="email" type="email" required autofocus
                   autocomplete="username" placeholder="Email"/>
            @error('email')
            <span class="d-block text-danger small mb-2">{{ $message }}</span>
            @enderror

            <input class="form-control mb-2" wire:model="password" id="password" type="password" required
                   name="password" placeholder="New Password" autocomplete="new-password"/>
            @error('password')
            <span class="d-block text-danger small mb-2">{{ $message }}</span>
            @enderror

            <input class="form-control mb-4" wire:model="password_confirmation" id="password_confirmation"
                   name="password_confirmation" type="password" required placeholder="Confirm Password"
                   autocomplete="new-password"/>
            @error('password_confirmation')
            <span class="d-block text-danger small mb-2">{{ $message }}</span>
            @enderror

            <button class="btn btn-primary w-100" type="submit">Reset Password</button>
        </form>
    </div>

</div>
