<?php

namespace App\Http\Controllers\Auth;

use App\Events\Auth\EmailVerified;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;


class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route("dashboard").'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user())); // run default verification
            event(new EmailVerified($request->user()));
        }

        return redirect()->intended(route("dashboard").'?verified=1');
    }
}
