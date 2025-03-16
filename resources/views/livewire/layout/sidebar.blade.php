<?php

use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component
{

}


?>

<nav class="navbar navbar-vertical navbar-expand-lg">
    <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
        <div class="navbar-vertical-content">
            <ul class="navbar-nav flex-column" id="navbarVerticalNav">
                <li class="nav-item">
                    <div class="nav-item-wrapper">

                        {!! getUserMenu(\App\Classes\ApplicationEnvironment::$id) !!}

                    </div>

                </li>
            </ul>
        </div>
    </div>
</nav>
