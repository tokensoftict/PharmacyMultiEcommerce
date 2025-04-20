

@push('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix.'admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix.'backend.admin.order.list') }}">Order List</a></li>
    <li class="breadcrumb-item active">Order #{{ $this->order->order_id }}</li>
@endpush
@script
<script>
    window.removeEventListener('refreshPage', refreshPage);

    function refreshPage() {
        setTimeout(function(){
            window.location.reload();
        }, 1000 )
    }

    window.addEventListener('refreshPage', refreshPage);

</script>
@endscript


<div>
    <div class="row align-items-center justify-content-between g-3 mt-n5">
        <div class="col-auto">
            <h2 class="mb-0">Order #{{ $this->order->order_id }}</h2>
        </div>
        <div class="col-auto">
            <div class="row g-3">
                <div class="d-flex">
                    <a href="#" class="btn btn-link pe-3 ps-0 text-body"><span class="fas fa-print me-2"></span>Print</a>
                    <button onclick="Livewire.getByName('backend.admin.order.show-order')[0].rePackOrder(); return false" wire:target="rePackOrder" wire:loading.attr="disabled" class="btn btn-link px-3 text-body">
                        <span wire:loading.remove="rePackOrder" class="fas fa-undo me-2"></span>
                        <span wire:loading wire:target="rePackOrder" class="spinner-border spinner-border-sm me-2" role="status"></span>
                        Re-pack
                    </button>
                    <div class="dropdown"><button class="btn text-body dropdown-toggle dropdown-caret-none ps-3 pe-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">More action<span class="fas fa-chevron-down ms-2"></span></button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix.'backend.admin.order.update', $this->order->id) }}">Edit Order</a></li>
                            <li><a class="dropdown-item" href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix.'backend.admin.order.update_product', $this->order->id) }}">Edit Order Products</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-5 gy-7">
        <div class="col-12 col-xl-8 col-xxl-9">
            <div class="card shadow-none border my-4">
                <div class="card-body p-0">
                    <div class="p-4">
                        <div id="orderTable">
                            <div class="table-responsive scrollbar">
                                <table class="table fs-9 mb-0">
                                    <thead>
                                    <tr>
                                        <th class="sort white-space-nowrap align-middle fs-10">#</th>
                                        <th class="sort white-space-nowrap align-middle">Product</th>
                                        <th class="sort align-middle text-end ps-4">Rate</th>
                                        <th class="sort align-middle text-end ps-4">Quantity</th>
                                        <th class="sort align-middle text-end ps-4">Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($this->order->order_products as $product)
                                        <tr>
                                            <td class="sort white-space-nowrap align-middle fs-10">{{ $loop->iteration }}</td>
                                            <td class="sort white-space-nowrap align-middle">{{ $product->name }}</td>
                                            <td class="sort align-middle text-end ps-4">{{ money($product->price) }}</td>
                                            <td class="sort align-middle text-end ps-4">{{ $product->quantity }}</td>
                                            <td class="sort align-middle text-end ps-4">{{ money($product->total) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    @foreach($this->order->order_total_orders as $orderTotal)
                                        <tr>
                                            <th></th>
                                            <th colspan="3"  class="sort align-middle text-end ps-4">{{ $orderTotal->name }}</th>
                                            <th  class="sort align-middle text-end ps-4">{{ money($orderTotal->value) }}</th>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th></th>
                                        <th colspan="3"  class="sort align-middle text-end ps-4">Total</th>
                                        <th  class="sort align-middle text-end ps-4">{{ money($this->order->total) }}</th>
                                    </tr>
                                    </tfoot>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-none border my-4">
                <div class="card-body p-0">
                    <div class="p-4">
                        <div class="row gx-4 gy-6 g-xl-7 justify-content-sm-center justify-content-xl-start">
                            <div class="col-12 col-sm-auto">
                                <h4 class="mb-5">Billing details</h4>
                                <div class="row g-4 flex-sm-column">
                                    <div class="col-6 col-sm-12">
                                        <div class="d-flex align-items-center mb-1"><span class="me-2" data-feather="user" style="stroke-width:2.5;"></span>
                                            <h6 class="mb-0">Customer</h6>
                                        </div><a class="d-block fs-9 ms-4" href="#!">{{ $this->order->firstname }} {{ $this->order->lastname }}</a>
                                    </div>
                                    <div class="col-6 col-sm-12">
                                        <div class="d-flex align-items-center mb-1"><span class="me-2" data-feather="mail" style="stroke-width:2.5;"></span>
                                            <h6 class="mb-0">Email</h6>
                                        </div><a class="d-block fs-9 ms-4" href="mailto:{{ $this->order->customer->user->email }}">{{ $this->order->customer->user->email }}</a>
                                    </div>
                                    <div class="col-6 col-sm-12 order-sm-1">
                                        <div class="d-flex align-items-center mb-1"><span class="me-2" data-feather="home" style="stroke-width:2.5;"></span>
                                            <h6 class="mb-0">Address</h6>
                                        </div>
                                        <div class="ms-4">
                                            <p class="text-body-secondary mb-0 fs-9">{{ $this->order->address->name }}</p>
                                            <p class="text-body-secondary mb-0 fs-9">{{ $this->order->address->address_1 }}<br class="d-none d-sm-block" />{{ $this->order->address->state->name }}, {{ $this->order->address->town->name }}</p>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-12">
                                        <div class="d-flex align-items-center mb-1"><span class="me-2" data-feather="phone" style="stroke-width:2.5;">  </span>
                                            <h6 class="mb-0">Phone</h6>
                                        </div><a class="d-block fs-9 ms-4" href="mailto:{{ $this->order->customer->user->phone }}">{{ $this->order->customer->user->phone }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-auto">
                                <h4 class="mb-5">Shipping details</h4>
                                <div class="row g-4 flex-sm-column">
                                    <div class="col-6 col-sm-12">
                                        <div class="d-flex align-items-center mb-1"><span class="me-2" data-feather="mail" style="stroke-width:2.5;">  </span>
                                            <h6 class="mb-0">Email</h6>
                                        </div><a class="d-block fs-9 ms-4" href="mailto:{{ $this->order->customer->user->email }}">{{ $this->order->customer->user->email }}</a>
                                    </div>
                                    <div class="col-6 col-sm-12">
                                        <div class="d-flex align-items-center mb-1"><span class="me-2" data-feather="phone" style="stroke-width:2.5;">  </span>
                                            <h6 class="mb-0">Phone</h6>
                                        </div><a class="d-block fs-9 ms-4" href="mailto:{{ $this->order->customer->user->phone }}">{{ $this->order->customer->user->phone }}</a>
                                    </div>
                                    <div class="col-6 col-sm-12 order-sm-1">
                                        <div class="d-flex align-items-center mb-1"><span class="me-2" data-feather="home" style="stroke-width:2.5;">  </span>
                                            <h6 class="mb-0">Address</h6>
                                        </div>
                                        <div class="ms-4">
                                            <p class="text-body-secondary mb-0 fs-9">{{ $this->order->address->name }}</p>
                                            <p class="text-body-secondary mb-0 fs-9">{{ $this->order->address->address_1 }}<br class="d-none d-sm-block" />{{ $this->order->address->state->name }}, {{ $this->order->address->town->name }}</p>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-12">
                                        <div class="d-flex align-items-center mb-1"><span class="me-2" data-feather="calendar" style="stroke-width:2.5;"></span>
                                            <h6 class="mb-0">Shipping Date</h6>
                                        </div>
                                        <p class="mb-0 text-body-secondary fs-9 ms-4">12 Nov, 2021</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4 col-xxl-3">
            <div class="card shadow-none border my-4">
                <div class="card-body p-0">
                    <div class="p-4">
                        <h3 class="card-title mb-4">Order Details</h3>
                        <div>
                            <div class="d-flex justify-content-between">
                                <p class="text-body fs-9">Invoice No :</p>
                                <p class="text-body-emphasis fw-semibold fs-9">{{ $this->order->invoice_no }}</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="text-body fs-9">Order ID :</p>
                                <p class="text-body-emphasis fw-semibold fs-9">{{ $this->order->order_id }}</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="text-body fs-9">Status :</p>
                                <p class="text-body-emphasis fw-semibold fs-9">{!! showStatus($this->order->status_id) !!}</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="text-body fs-9">Payment Method :</p>
                                <p class="text-body-emphasis fw-semibold fs-9">{{ $this->order->payment_method->name }}</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="text-body fs-9">Delivery Method :</p>
                                <p class="text-body-emphasis fw-semibold fs-9">{{ $this->order->delivery_method->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-none border my-4">
                <div class="card-body p-0">
                    <div class="p-4">
                        <h3 class="card-title mb-4">Customer Details</h3>
                        <div>
                            <div class="d-flex justify-content-between">
                                <p class="text-body fs-9">Name :</p>
                                <p class="text-body-emphasis fw-semibold fs-9">{{ $this->order->firstname }} {{ $this->order->lastname }}</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="text-body fs-9">Phone :</p>
                                <p class="text-body-emphasis fw-semibold fs-9">{{ $this->order->customer->phone }}</p>
                            </div>
                            @if($this->order->customer_type === \App\Models\WholesalesUser::class)
                                <div class="d-flex justify-content-between">
                                    <p class="text-body fs-9">Business Name :</p>
                                    <p class="text-body-emphasis fw-semibold fs-9">{{ $this->order->customer->business_name }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

