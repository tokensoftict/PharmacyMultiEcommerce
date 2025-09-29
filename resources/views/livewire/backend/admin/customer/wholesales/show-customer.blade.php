
@push('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix.'admin.dashboard') }}">Dashboard</a></li>
    @if(\App\Classes\ApplicationEnvironment::$stock_model === \App\Models\WholessalesStockPrice::class)
        <li class="breadcrumb-item"><a href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix.'backend.admin.wholesales.customer_manager.list') }}">Customer List</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix.'backend.admin.supermarket.customer_manager.list') }}">Customer List</a></li>
    @endif
    <li class="breadcrumb-item active">Customer #{{ $this->wholesalesUser->id }}</li>
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
@php
    $myCarts =   $this->wholesalesUser->getCart()
@endphp
<div>
    <div class="row align-items-center justify-content-between g-3 mt-n5">
        <div class="col-auto">
            <h2 class="mb-0">Customer details</h2>
        </div>
        <div class="col-auto">
            <div class="row g-3">
                @if($this->wholesalesUser instanceof \App\Models\WholesalesUser)
                    @if( $this->wholesalesUser->status == "0")
                        <div class="col-auto"><button wire:click="approveStore"  wire:confirm="Are you sure you want approved this business, this can not be reversed?" class="btn btn-primary"><span class="fa-solid fa-check"></span> Approve Store</button></div>
                    @endif
                @endif
                <div class="col-auto"> <button type="button"  onclick="Livewire.getByName('backend.component.resetpassword')[0].openModal(); return false;" class="btn btn-danger"><span class="fas fa-key me-2"></span>
                        Reset password
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-5 mt-2">
        <div class="col-12 col-xxl-4">
            <div class="row g-3 h-100">
                <div class="col-12 col-md-7 col-xxl-12">
                    <div class="card h-100 h-xxl-auto">
                        <div class="card-body d-flex flex-column justify-content-between pb-3">
                            <div class="row align-items-center g-5 mb-3 text-center text-sm-start">
                                <div class="col-12 col-sm-auto mb-sm-2">
                                    <div class="avatar avatar-5xl"><img class="rounded-circle" src="{{ asset($this->wholesalesUser->user->image) }}" alt="" /></div>
                                </div>
                                <div class="col-12 col-sm-auto flex-1">
                                    <h3>{{ $this->wholesalesUser->user->name }}</h3>
                                    <p class="text-body-secondary">Joined {{ (new \Carbon\Carbon($this->wholesalesUser->user->created_at))->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="d-flex flex-between-center border-top border-dashed pt-4">
                                <div>
                                    <h6>Total Order(s)</h6>
                                    <p class="fs-9 text-body-secondary mb-0">{{ $this->wholesalesUser->order->count() }}</p>
                                </div>
                                <div>
                                    <h6>Total Spent</h6>
                                    <p class="fs-9 text-body-secondary mb-0">{{ money($this->wholesalesUser->order->sum(function($item){
                                        if($item->status_id === status('Paid') || $item->status_id === status('Complete') || $item->status_id === status('Dispatched')) {
                                            return $item->total;
                                        }
                                        return 0;
                                    })) }}</p>
                                </div>
                                <div>
                                    <h6 class="text-center">Last Login</h6>
                                    <p class="fs-9 text-body-secondary mb-0">{{ $this->wholesalesUser?->user?->last_seen?->format("jS, M Y - H:i a") ?? "N/A"  }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-5 col-xxl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <h3 class="me-1">Account Details</h3>
                                <button class="btn btn-link p-0" data-toggle="modal" data-target="#updateCustomer"><span class="fas fa-pen fs-8 ms-3 text-body-quaternary"></span></button>
                            </div>

                            <div class="d-flex justify-content-between">
                                <p class="text-body fs-9">Account Type :</p>
                                <p class="text-body-emphasis fw-semibold fs-9">{{ $this->wholesalesUser->user->supermarket_user?->customer_type->name ?? "N/A" }}</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="text-body fs-9">Account Group :</p>
                                <p class="text-body-emphasis fw-semibold fs-9">{{ $this->wholesalesUser->user->supermarket_user?->customer_group->name ?? "N/A" }}</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="text-body fs-9">Phone Number :</p>
                                <p class="text-body-emphasis fw-semibold fs-9">{{ $this->wholesalesUser->user->phone ?? "N/A" }}</p>
                            </div>

                        </div>
                    </div>
                </div>

                <div id="updateCustomer" class="modal fade" role="dialog">
                    <form method="post" wire:submit.prevent="updateCustomer">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Update Phone Number</h4>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input class="form-control" type="text" wire:model="customerData.phone" name="phone_number" placeholder="Phone Number">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" wire:target="updateCustomer" wire:loading.attr="disabled" class="btn btn-primary">
                                    Update Phone Number
                                    <span wire:loading wire:target="updateCustomer" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>

                @if($this->wholesalesUser instanceof \App\Models\WholesalesUser)
                    <div class="col-12 col-md-5 col-xxl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <h3 class="me-1">Store Details</h3>
                                    <button class="btn btn-link p-0"><span class="fas fa-pen fs-8 ms-3 text-body-quaternary"></span></button>
                                </div>
                                <h5 class="text-body-secondary mb-3">{{ strtoupper($this->wholesalesUser->business_name) }}</h5>

                                <div class="d-flex justify-content-between">
                                    <p class="text-body fs-9">Customer Type :</p>
                                    <p class="text-body-emphasis fw-semibold fs-9">{{ $this->wholesalesUser?->customer_type->name ?? "N/A" }}</p>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <p class="text-body fs-9">Customer Group :</p>
                                    <p class="text-body-emphasis fw-semibold fs-9">{{ $this->wholesalesUser?->customer_group->name ?? "N/A" }}</p>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <p class="text-body fs-9">CAC Document :</p>
                                    <a target="_blank" href="{{ $this->wholesalesUser?->cac_document ?? "#" }}" class="fw-semibold fs-9">View Document</a>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <p class="text-body fs-9">Premises Licence :</p>
                                    <a target="_blank" href="{{ $this->premises_licence?->premises_licence ?? "#" }}" class="fw-semibold fs-9">View Document</a>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <p class="text-body fs-9">Business Phone Number :</p>
                                    <p class="text-body-emphasis fw-semibold fs-9">{{ $this->wholesalesUser->phone ?? "N/A" }}</p>
                                </div>

                                @if(!is_null($this->wholesalesUser->sales_representative_id))
                                    <div class="d-flex justify-content-between">
                                        <p class="text-body fs-9">Sales Representative :</p>
                                        <p class="text-body-emphasis fw-semibold fs-9">{{ $this->wholesalesUser->sales_representative->name ?? "N/A" }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if(!is_null($this->wholesalesUser->address_id))
                    <div class="col-12 col-md-5 col-xxl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <h3 class="me-1">Default Address</h3>
                                    <button class="btn btn-link p-0"><span class="fas fa-pen fs-8 ms-3 text-body-quaternary"></span></button>
                                </div>
                                <h5 class="text-body-secondary">Address</h5>
                                <p class="text-body-secondary">{{ $this->wholesalesUser?->address->name }}<br />
                                    {{ $this->wholesalesUser?->address->address_1 }}<br />{{ $this->wholesalesUser?->address?->state->name }}, {{ $this->wholesalesUser->address?->town->name }}
                                </p>
                                <div class="mb-3">
                                    <h5 class="text-body-secondary">Email</h5><a href="mailto:{{ $this->wholesalesUser->user->email }}">{{  $this->wholesalesUser->user->email }}</a>
                                </div>
                                <h5 class="text-body-secondary">Phone</h5><a class="text-body-secondary" href="tel:{{  $this->wholesalesUser->user->phone }}">{{  $this->wholesalesUser->user->phone }}</a>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
        <div class="col-12 col-xxl-8">
            <div class="mb-6">
                <h3 class="mb-4">Orders <span class="text-body-tertiary fw-normal">({{ $this->wholesalesUser->order->count() }})</span></h3>
                <div class="border-top border-bottom border-translucent" id="customerOrdersTable">
                    <div class="table-responsive scrollbar">
                        <table class="table table-sm fs-9 mb-0">
                            <thead>
                            <tr>
                                <th class="sort white-space-nowrap align-middle ps-0 pe-3">Order</th>
                                <th class="sort align-middle text-end pe-7">Total</th>
                                <th class="sort align-middle white-space-nowrap pe-3">Status</th>
                                <th class="sort align-middle white-space-nowrap text-start pe-3">Payment Method</th>
                                <th class="sort align-middle white-space-nowrap text-start">Delivery Method</th>
                                <th class="sort align-middle text-end pe-0">Date</th>
                                <th class="sort text-end align-middle pe-0 ps-5"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($this->wholesalesUser->order as $order)
                                <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                    <td class="order align-middle white-space-nowrap ps-0"><a class="fw-semibold" href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix.'backend.admin.order.view', $order->id) }}">#{{ $order->id }}</a></td>
                                    <td class="total align-middle text-end fw-semibold pe-7 text-body-highlight">{{ money($order->total) }}</td>
                                    <td class="payment_status align-middle white-space-nowrap text-start fw-bold text-body-tertiary">{!! showStatus($order->status_id) !!}</td>
                                    <td class="fulfilment_status align-middle white-space-nowrap text-start fw-bold text-body-tertiary">{{ $order->payment_method->name }}</td>
                                    <td class="delivery_type align-middle white-space-nowrap text-body fs-9 text-start">{{ $order->delivery_method->name }}</td>
                                    <td class="date align-middle white-space-nowrap text-body-tertiary fs-9 ps-4 text-end">{{ $order->order_date->format('D, F jS, Y') }}</td>
                                    <td class="align-middle white-space-nowrap text-end pe-0 ps-5">
                                        <div class="btn-reveal-trigger position-static"><button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs-10" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs-10"></span></button>
                                            <div class="dropdown-menu dropdown-menu-end py-2">
                                                <a class="dropdown-item" href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix.'backend.admin.order.view', $order->id) }}">View</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($this->wholesalesUser->order->count() > 6)
                        <div class="row align-items-center justify-content-between py-2 pe-0 fs-9">
                            <div class="col-auto d-flex">
                                <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body" data-list-info="data-list-info"></p>
                                <a class="fw-semibold" href="#!" data-list-view="*">View all<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
                                <a class="fw-semibold d-none" href="#!" data-list-view="less">View Less<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
                            </div>
                            <div class="col-auto d-flex"><button class="page-link" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
                                <ul class="mb-0 pagination"></ul>
                                <button class="page-link pe-0" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="mb-6">
                <h3 class="mb-4">Shopping Cart <span class="text-body-tertiary fw-normal">({{ $myCarts->count() }})</span></h3>
                <div class="border-translucent border-top border-bottom" id="customerWishlistTable" data-list='{"valueNames":["products","color","size","price","quantity","total"],"page":5,"pagination":true}'>
                    <div class="table-responsive scrollbar">
                        <table class="table fs-9 mb-0">
                            <thead>
                            <tr>
                                <th class="sort white-space-nowrap align-middle fs-10" scope="col" style="width:5%;"></th>
                                <th class="sort white-space-nowrap align-middle" scope="col" style="width:35%; min-width:250px;" data-sort="products">Product</th>
                                <th class="sort align-middle" scope="col" data-sort="size" style="width:10%;">Quantity</th>
                                <th class="sort align-middle text-end" scope="col" data-sort="price" style="width:15%;">Price</th>
                                <th class="sort align-middle text-end" scope="col" data-sort="total" style="width:15%;">Total</th>
                            </tr>
                            </thead>
                            <tbody class="list" id="customer-wishlist-table-body">
                            @foreach($myCarts as $item)
                                <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                    <td class="align-middle white-space-nowrap py-1">
                                        <a class="border border-translucent rounded-2 d-inline-block" href="#">
                                            <img src="{{ $item->product_image }}" alt="" width="40" height="40" /></a>
                                    </td>
                                    <td class="products align-middle"><a class="fw-semibold mb-0" href="#">{{ $item->name }}</a></td>
                                    <td class="size align-middle white-space-nowrap text-body-tertiary fs-9 fw-semibold">{{ $item->cart_quantity }}</td>
                                    <td class="price align-middle text-body fs-9 fw-semibold text-end">{{ money($item->price) }}</td>
                                    <td class="total align-middle fw-bold text-body-highlight text-end">{{ money($item->price * $item->cart_quantity) }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @livewire("backend.component.resetpassword", ['user' => $this->wholesalesUser->user], "reset-sales-rep-password")
</div>
