<?php

use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Session;
use function Livewire\Volt\{state};

new #[Layout('layout.auth')] class extends Component
{
    public $myapps;

    public function mount()
    {
        $this->myapps = auth()->user()->app_users()->with('app')->whereHas('app', function($query){
            $query->where("show", '1');
        })->get();

    }

}
?>

<div>
    <div class="row">
        @foreach($myapps as $myapp)
            <div class="col-6 mt-2">
                <a class="btn btn-success" href="{{ $myapp->app->link }}">{{ $myapp->app->name }}</a>
            </div>
        @endforeach
    </div>
</div>
