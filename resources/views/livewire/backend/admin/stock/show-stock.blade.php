
@push('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix.'admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix.'backend.admin.stock_manager.list_stock') }}">Stock List</a></li>
    <li class="breadcrumb-item active">Stock #{{ $this->selectedStock->stock->id }}</li>
@endpush

<div>

    <div class="row align-items-center justify-content-between g-3 mt-n5">
        <div class="col-auto">
            <h2 class="mb-0">Stock details</h2>
        </div>
        <div class="col-auto">
            <div class="row g-3">
                <!--<div class="col-auto"><button class="btn btn-phoenix-danger"><span class="fa-solid fa-trash-can me-2"></span>Delete customer</button></div> -->
                <button type="button"  class="btn btn-phoenix-danger"><span class="fas fa-key me-2"></span>
                    Disable Stock
                </button>
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
                                <div class="col-12  mb-sm-2 text-center">
                                    <div class="avatar "><img class="img-fluid" src="{{ asset($this->selectedStock->stock->product_image) }}" alt="" /></div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <h3 class="me-1">Description</h3>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="text-body-emphasis fw-semibold fs-9">{{ $this->selectedStock->stock->description  ?? "N/A" }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xxl-8">
            <div class="mb-6">

                <div class="card h-100 h-xxl-auto">
                    <div class="card-body d-flex flex-column justify-content-between pb-3">
                        <div class="d-flex align-items-center mb-3">
                            <h3 class="me-1">Stock Information</h3>
                        </div>


                        <div class="d-flex justify-content-between">
                            <p class="text-body fs-9">Name :</p>
                            <p class="text-body-emphasis fw-semibold fs-9">{{ $this->selectedStock->stock->name }}</p>
                        </div>
                        <div class="d-flex justify-content-between">
                            <p class="text-body fs-9">Category :</p>
                            <p class="text-body-emphasis fw-semibold fs-9">{{ $this->selectedStock->stock->productcategory->name  ?? "N/A" }}</p>
                        </div>
                        <div class="d-flex justify-content-between">
                            <p class="text-body fs-9">Manufacturer :</p>
                            <p class="text-body-emphasis fw-semibold fs-9">{{ $this->selectedStock->stock->manufacturer->name ?? "N/A" }}</p>
                        </div>

                        <div class="d-flex justify-content-between">
                            <p class="text-body fs-9">Classification :</p>
                            <p class="text-body-emphasis fw-semibold fs-9">{{ $this->selectedStock->stock->classification->name ?? "N/A" }}</p>
                        </div>

                        <div class="d-flex justify-content-between">
                            <p class="text-body fs-9">Group :</p>
                            <p class="text-body-emphasis fw-semibold fs-9">{{ $this->selectedStock->stock->productgroup->name ?? "N/A" }}</p>
                        </div>

                        <div class="d-flex justify-content-between">
                            <p class="text-body fs-9">Box :</p>
                            <p class="text-body-emphasis fw-semibold fs-9">{{ $this->selectedStock->stock->box ?? "N/A" }}</p>
                        </div>

                        <div class="d-flex justify-content-between">
                            <p class="text-body fs-9">Max :</p>
                            <p class="text-body-emphasis fw-semibold fs-9">{{ $this->selectedStock->stock->max ?? "N/A" }}</p>
                        </div>

                        <div class="d-flex justify-content-between">
                            <p class="text-body fs-9">Carton :</p>
                            <p class="text-body-emphasis fw-semibold fs-9">{{ $this->selectedStock->stock->carton ?? "N/A" }}</p>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <h3 class="me-1">Price Information</h3>
                        </div>
                        @if(isset($this->selectedStock->stock->wholessales_stock_prices->price))
                        <div class="d-flex justify-content-between">
                            <p class="text-body fs-9">Wholesales Price :</p>
                            <p class="text-body-emphasis fw-semibold fs-9 bold"><b>{{$this->selectedStock->stock->wholessales_stock_prices->price ? money($this->selectedStock->stock->wholessales_stock_prices->price) : "N/A" }}</b></p>
                        </div>
                        @endif

                        @if(isset($this->selectedStock->stock->supermarkets_stock_prices->price))
                            <div class="d-flex justify-content-between">
                                <p class="text-body fs-9">Supermarket Price :</p>
                                <p class="text-body-emphasis fw-semibold fs-9 bold"><b>{{$this->selectedStock->stock->supermarkets_stock_prices->price ? money($this->selectedStock->stock->supermarkets_stock_prices->price) : "N/A" }}</b></p>
                            </div>
                        @endif

                        <div class="d-flex align-items-center mb-3">
                            <h3 class="me-1">Status Information</h3>
                        </div>


                        <div class="d-flex justify-content-between">
                            <p class="text-body fs-9">Wholesales Status :</p>
                            <p class="text-body-emphasis fw-semibold fs-9">{!! $this->selectedStock->stock->wholessales_stock_prices->status ? label('ACTIVE', 'success') : label('DISABLED', 'danger') !!}</p>
                        </div>

                        <div class="d-flex justify-content-between">
                            <p class="text-body fs-9">Supermarket Status :</p>
                            <p class="text-body-emphasis fw-semibold fs-9">{!! $this->selectedStock->stock->supermarkets_stock_prices->status ? label('ACTIVE', 'success') : label('DISABLED', 'danger') !!}</p>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <h3 class="me-1">Quantity Information</h3>
                        </div>

                        <div class="d-flex justify-content-between">
                            <p class="text-body fs-9">Wholesales Quantity :</p>
                            <p class="text-body-emphasis fw-semibold fs-9">{{ $this->selectedStock->stock->wholessales_stock_prices->quantity }}</p>
                        </div>

                        <div class="d-flex justify-content-between">
                            <p class="text-body fs-9">Supermarket Quantity :</p>
                            <p class="text-body-emphasis fw-semibold fs-9">{{ $this->selectedStock->stock->supermarkets_stock_prices->quantity }}</p>
                        </div>


                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
