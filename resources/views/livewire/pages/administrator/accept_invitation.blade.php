<?php

use App\Mail\Administrator\AdminAcceptInvitation;
use App\Mail\SalesRep\AcceptInvitation;
use App\Models\SalesRepresentative;
use App\Models\SupermarketAdmin;
use App\Models\WholesalesAdmin;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new #[Layout('layout.app_frontend')] class extends Component {
    public string $token;
    private $administrator;

    public function mount()
    {
        $this->administrator = WholesalesAdmin::where('token', $this->token)->first();
        if (!$this->administrator) {
            $this->administrator = SupermarketAdmin::where('token', $this->token)->first();
        }

        if ($this->administrator) {
            $this->administrator->token = NULL;
            $this->administrator->invitation_status = true;
            $this->administrator->status = true;
            $this->administrator->invitation_approval_date = now()->format("Y-m-d");
            $this->administrator->save();
            Mail::to($this->administrator->user->email)->send(new AdminAcceptInvitation($this->administrator));
        }
    }

}
?>
<div>
    @if($this->administrator)
        <style>
            body {
                background: #fff5f5;
                font-family: 'Segoe UI', sans-serif;
            }

            .card {
                border: none;
                border-radius: 15px;
                box-shadow: 0 5px 30px rgba(255, 0, 0, 0.1);
            }

            .btn-red {
                background-color: #e53935;
                color: #fff;
            }

            .btn-red:hover {
                background-color: #c62828;
                color: #fff;
            }

            .emoji {
                font-size: 2rem;
            }
        </style>
        <div class="container d-flex align-items-center justify-content-center vh-100">
            <div class="col-md-8 col-lg-6">
                <div class="card p-4 text-center">
                    <div class="emoji mb-3">🛡️</div>
                    <h2 class="mb-3 text-danger">Administrator Access Granted!</h2>
                    <p class="lead">
                        You're now an Administrator of the platform 🔧📊
                    </p>
                    <p class="mb-4">
                        Manage users, oversee platform settings, and keep operations running smoothly. Your leadership
                        helps shape the future of our system 🚀
                    </p>
                    <a href="{{ route('customer.index') }}" class="btn btn-red btn-lg">Continue</a>
                    <div class="mt-4 text-muted small">
                        Powered by
                        <strong>{{ app(\App\Classes\Settings::class)->get("name", "PS GENERAL DRUGS CENTRE PHARMACY.") }}</strong>
                    </div>
                </div>
            </div>
        </div>
    @else
        <style>
            body {
                background: #fff5f5;
                font-family: 'Segoe UI', sans-serif;
            }

            .card {
                border: none;
                border-radius: 15px;
                box-shadow: 0 5px 30px rgba(255, 0, 0, 0.1);
            }

            .btn-red {
                background-color: #e53935;
                color: #fff;
            }

            .btn-red:hover {
                background-color: #c62828;
                color: #fff;
            }

            .emoji {
                font-size: 3rem;
            }
        </style>
        <div class="container d-flex align-items-center justify-content-center vh-100">
            <div class="col-md-8 col-lg-6">
                <div class="card p-4 text-center">
                    <div class="emoji mb-3">😕</div>
                    <h2 class="mb-3 text-danger">Oops! Invitation Invalid or Expired</h2>
                    <p class="lead">
                        Unfortunately, this invitation link is no longer valid.
                    </p>
                    <p class="mb-4">
                        This might be because it's already been used or has expired.
                        If you think this is a mistake, please contact our support team.
                    </p>
                    <a href="{{ route('customer.index') }}" class="btn btn-red btn-lg">Continue</a>
                    <div class="mt-4 text-muted small">
                        Powered by
                        <strong>{{ app(\App\Classes\Settings::class)->get("name", "PS GENERAL DRUGS CENTRE PHARMACY.") }}</strong>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
