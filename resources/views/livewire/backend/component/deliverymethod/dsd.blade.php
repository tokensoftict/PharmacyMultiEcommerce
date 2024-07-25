<?php

use function Livewire\Volt\{state};
use Livewire\Volt\Component;
use App\Models\DeliveryMethod;
use Illuminate\Support\Collection;

new class  extends Component {
    public DeliveryMethod $deliveryMethod;

}

?>

@section('pageHeaderTitle')
    {{ $this->deliveryMethod->name }} Settings
@endsection

@push('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('backend.admin.settings.delivery_methods') }}">Delivery Methods</a></li>
    <li class="breadcrumb-item active">{{ $this->deliveryMethod->name }} Settings</li>
@endpush

<div>
    <div class="card shadow-none border my-4">
        <div class="card-body p-0">
            <div class="p-4">
                <div class="row tx-14">
                    <h1>Nothing to do here.</h1>
                </div>
            </div>
        </div>
    </div>
</div>
