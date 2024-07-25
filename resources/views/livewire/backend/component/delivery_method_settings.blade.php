<?php

use function Livewire\Volt\{state};
use Livewire\Volt\Component;
use App\Models\DeliveryMethod;

new class  extends Component
{
    public DeliveryMethod $deliveryMethod;
}

?>

<div>
    @livewire('backend.component.deliverymethod.'.strtolower($deliveryMethod->code), ['deliveryMethod' => $deliveryMethod])
</div>
