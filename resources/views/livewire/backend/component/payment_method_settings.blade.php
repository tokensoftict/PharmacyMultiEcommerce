<?php

use function Livewire\Volt\{state};
use Livewire\Volt\Component;
use App\Models\PaymentMethod;

new class  extends Component
{
    public PaymentMethod $paymentMethod;
}

?>

<div>
    @livewire('backend.component.paymentmethod.'.strtolower($paymentMethod->code), ['paymentMethod' => $paymentMethod])
</div>
