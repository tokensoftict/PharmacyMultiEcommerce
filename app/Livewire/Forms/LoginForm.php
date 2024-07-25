<?php

namespace App\Livewire\Forms;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Rule;
use Livewire\Form;

class LoginForm extends Form
{
    #[Rule('required|string|email')]
    public string $email = '';

    #[Rule('required|string')]
    public string $password = '';

    #[Rule('boolean')]
    public bool $remember = false;

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {

        $credentials = ['email' => $this->email, 'password' => $this->password];

        if (! Auth::attempt($credentials, $this->remember)) {
            throw ValidationException::withMessages([
                'form.email' => 'These credentials do not match our records.',
            ]);
        }
         \auth()->user()->updateLastSeen();
    }

}
