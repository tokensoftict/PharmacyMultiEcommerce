<?php

use App\Mail\SalesRep\AcceptInvitation;
use App\Models\SalesRepresentative;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new #[Layout('layout.app_frontend')] class extends Component {


}
?>
<div>
    <style>
        body {
            background: #fff5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            border: none;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 0 25px rgba(255, 0, 0, 0.15);
            background-color: #ffffff;
            max-width: 500px;
            text-align: center;
        }

        .check-icon {
            font-size: 3rem;
            color: #dc3545; /* Bootstrap's red */
        }

        .btn-red {
            background-color: #dc3545;
            border: none;
        }

        .btn-red:hover {
            background-color: #c82333;
        }
    </style>

    <div class="card">
        <div class="check-icon mb-3">✔️</div>
        <h3 class="mb-2 text-danger">Email Verified</h3>
        <p class="text-muted">Your email has been successfully verified. Welcome aboard!</p>
        <a href="{{ route('customer.index') }}" class="btn btn-red text-white mt-3 px-4 py-2">Continue</a>
    </div>

</div>
