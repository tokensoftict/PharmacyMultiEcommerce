<?php

use App\Mail\SalesRep\AcceptInvitation;
use App\Models\SalesRepresentative;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new #[Layout('layout.app_frontend')] class extends Component {
    public string $token;
    private $salesRepresentative;

    public function mount()
    {
        $this->salesRepresentative = SalesRepresentative::where('token', $this->token)->first();
        if ($this->salesRepresentative) {
            $this->salesRepresentative->token = NULL;
            $this->salesRepresentative->invitation_status = true;
            $this->salesRepresentative->status  = true;
            $this->salesRepresentative->invitation_approval_date = now()->format("Y-m-d");
            $this->salesRepresentative->save();
            Mail::to($this->salesRepresentative->user->email)->send(new AcceptInvitation($this->salesRepresentative));
        }
    }

}
?>
<div>
    @if($this->salesRepresentative)
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
                    <div class="emoji mb-3">🎉</div>
                    <h2 class="mb-3 text-danger">Welcome to the Sales Rep Team!</h2>
                    <p class="lead">
                        You're now officially part of our amazing sales force 🚀
                    </p>
                    <p class="mb-4">
                        Start sharing your referral code, build your customer network, and earn rewards every time
                        someone places an order using your link 💼💰
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
