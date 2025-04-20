<?php

use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Session;
use function Livewire\Volt\{state};

new #[Layout('layout.app_select_store')] class extends Component
{
    public $myapps;

    public function mount()
    {
        $this->myapps = auth()->user()->app_users()->with(['app', 'user_type'])->whereHas('app', function($query){
            $query->where("show", '1')->where("type", "Backend");
        })->get();

    }

}
?>

<div class="container-fluid  min-vh-100 d-flex justify-content-center">
    <div class="col-md-8 col-lg-6 text-center px-3" style="margin-top: 5.0rem">

        <!-- Logo -->
        <div class="mb-4">
            <img src="{{ asset('logo/placholder.jpg') }}" alt="Company Logo" class="img-fluid" style="max-height: 100px;">
        </div>

        <!-- Welcome Text -->
        <div class="mb-4">
            <h2 class="fw-bold">Hello {{ auth()->user()->firstname }}, Welcome Back!</h2>
            <p class="text-muted">Choose the application you want to manage</p>
        </div>

        <!-- App List -->
        <div class="list-group shadow-sm rounded overflow-hidden">
            @foreach($myapps as $myapp)
                <a href="{{ $myapp->app->link }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center  border-0 py-4">
                    <div class="d-flex align-items-start flex-column">
                        <h5 class="mb-1  fw-semibold">{{ $myapp->app->name }}</h5>
                        <small class="text-muted d-block">{{ $myapp->app->description }}</small>
                        <small class=" d-block text-danger">Last seen : {{ $myapp->user_type->last_activity_date ? $myapp->user_type->last_activity_date->format("F jS, Y g:i A") : now()->format("F jS, Y g:i A") }}</small>
                    </div>
                    <span class="badge bg-danger text-white rounded-pill px-3 py-2">Enter</span>
                </a>
            @endforeach
        </div>

        <!-- Footer -->
        <div class="mt-5 text-muted small">
            Powered by <strong class="text-dark">{{ app(\App\Classes\Settings::class)->get("name", "PS GENERAL DRUGS CENTRE PHARMACY.") }} - <a class="mx-1" href="https://tokensoftict.com.ng/">Tokensoft ICT</a></strong>
        </div>
    </div>
</div>

