@push('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('backend.admin.sales_rep_manager.list') }}">Sales Representative List</a></li>
    <li class="breadcrumb-item active">Sales Representative #{{ $this->salesRepresentative->id }}</li>
@endpush

<div>

    <div class="row align-items-center justify-content-between g-3 mt-n5">
        <div class="col-auto">
            <h2 class="mb-0">Sales Representative Dashboard</h2>
        </div>
        <div class="col-auto">
            <div class="row g-3">
                <!--<div class="col-auto"><button class="btn btn-phoenix-danger"><span class="fa-solid fa-trash-can me-2"></span>Delete customer</button></div> -->
                <div class="col-auto">
                    <button type="button"  onclick="Livewire.getByName('backend.component.resetpassword')[0].openModal(); return false;" class="btn btn-phoenix-danger"><span class="fas fa-key me-2"></span>
                        Reset password
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-5 mt-2">
        <div class="col-12 col-xxl-4">
            <div class="col-12 col-md-7 col-xxl-12">
                <div class="card h-100 h-xxl-auto">
                    <div class="card-body d-flex flex-column justify-content-between pb-3">
                        <div class="row align-items-center g-5 mb-3 text-center text-sm-start">
                            <div class="col-12 col-sm-auto mb-sm-2">
                                <div class="avatar avatar-5xl"><img class="rounded-circle" src="{{ asset($this->salesRepresentative->user->image) }}" alt="" /></div>
                            </div>
                            <div class="col-12 col-sm-auto flex-1">
                                <h3>{{ $this->salesRepresentative->user->name }}</h3>
                                <p class="text-body-secondary">Joined {{ (new \Carbon\Carbon($this->salesRepresentative->user->created_at))->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="d-flex flex-between-center border-top border-dashed pt-4">
                            <div>
                                <h6>Customer(s)</h6>
                                <p class="fs-9 text-center text-body-secondary mb-0">
                                    {{ \App\Models\WholesalesUser::where('sales_representative_id', $this->salesRepresentative->id)->count() }}
                                </p>
                            </div>
                            <div>
                                <h6>Customer Orders</h6>
                                <p class="fs-9 text-center text-body-secondary mb-0">
                                    {{ \App\Models\Order::where('sales_representative_id', $this->salesRepresentative->id)->count() }}
                                </p>
                            </div>
                            <div>
                                <h6 class="text-center">Last Login</h6>
                                <p class="fs-9 text-body-secondary mb-0">{{ $this->salesRepresentative?->user?->last_seen?->format("jS, M Y - H:i a") ?? "N/A" }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-5 col-xxl-12 mt-2">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <h3 class="me-1">Account Details</h3>
                            <button class="btn btn-link p-0"><span class="fas fa-pen fs-8 ms-3 text-body-quaternary"></span></button>
                        </div>

                        <div class="d-flex justify-content-between">
                            <p class="text-body fs-9">Full Name :</p>
                            <p class="text-body-emphasis fw-semibold fs-9">{{ $this->salesRepresentative->user->name ?? "N/A" }}</p>
                        </div>
                        <div class="d-flex justify-content-between">
                            <p class="text-body fs-9">Email Address :</p>
                            <p class="text-body-emphasis fw-semibold fs-9">{{ $this->salesRepresentative->user->email ?? "N/A" }}</p>
                        </div>
                        <div class="d-flex justify-content-between">
                            <p class="text-body fs-9">Phone Number :</p>
                            <p class="text-body-emphasis fw-semibold fs-9">{{ $this->salesRepresentative->user->phone ?? "N/A" }}</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xxl-8">
            <div class="row justify-content-between">
                <div class="col-6 col-md-4 col-xxl-4 text-center border-translucent border-start-xxl border-end-xxl-0 border-bottom-xxl-0 border-end border-bottom pb-4 pb-xxl-0 ">
                    <span class="uil fs-5 lh-1 uil-users-alt text-primary"></span>
                    <h1 class="fs-5 pt-3"> {{ \App\Models\WholesalesUser::where('sales_representative_id', $this->salesRepresentative->id)->count() }}</h1>
                    <p class="fs-9 mb-0">Total Customers</p>
                </div>

                <div class="col-6 col-md-4 col-xxl-4 text-center border-translucent border-start-xxl border-end-xxl-0 border-bottom-xxl-0 border-end border-bottom pb-4 pb-xxl-0 ">
                    <span class="uil fs-5 lh-1 uil-graph-bar text-primary"></span>
                    <h1 class="fs-5 pt-3">{{ $totalDispatchedCount }}</h1>
                    <p class="fs-9 mb-0">{{ $this->month }} Total Dispatched Orders</p>
                </div>

                <div class="col-6 col-md-4 col-xxl-4 text-center border-translucent border-start-xxl border-end-xxl-0 border-bottom-xxl-0 border-end border-bottom pb-4 pb-xxl-0 ">
                    <span class="uil fs-5 lh-1 uil-arrow-growth text-primary"></span>
                    <h1 class="fs-5 pt-3">{{ money($totalDispatchedSum) }}</h1>
                    <p class="fs-9 mb-0">{{ $this->month }} Total Sales</p>
                </div>
            </div>
            <div class="row justify-content-between mt-8">
                <div id="members" data-list='{"valueNames":["customer","email","mobile_number","city","last_active","joined"],"page":10,"pagination":true}'>
                    <div class="row align-items-center justify-content-between g-3 mb-4">
                        <h2 class="text-bold text-body-emphasis mb-1">Customer List</h2>
                        <div class="col col-auto">
                            <div class="search-box">
                                <form class="position-relative"><input class="form-control search-input search" type="search" placeholder="Search customers" aria-label="Search" />
                                    <span class="fas fa-search search-box-icon"></span>
                                </form>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-primary" onclick="Livewire.getByName('backend.component.salesrep.addcustomer')[0].openModal(); return false;">
                                    <span class="fas fa-plus me-2"></span>
                                    Add Customer
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 px-lg-6 mb-5 bg-body-emphasis border-y mt-2 position-relative top-1">
                        <div class="table-responsive scrollbar ms-n1 ps-1">
                            <table class="table table-sm fs-9 mb-0">
                                <thead>
                                <tr>
                                    <th class="white-space-nowrap fs-9 align-middle ps-0">
                                        <div class="form-check mb-0 fs-8"><input class="form-check-input" id="checkbox-bulk-members-select" type="checkbox" data-bulk-select='{"body":"members-table-body"}' /></div>
                                    </th>
                                    <th class="sort align-middle" scope="col" data-sort="customer" style="width:15%; min-width:200px;">CUSTOMER</th>
                                    <th class="sort align-middle" scope="col" data-sort="email" style="width:15%; min-width:200px;">EMAIL</th>
                                    <th class="sort align-middle pe-3" scope="col" data-sort="mobile_number" style="width:20%; min-width:200px;">PHONE NUMBER</th>
                                    <th class="sort align-middle text-end" scope="col" data-sort="last_active" style="width:21%;  min-width:200px;">LAST ACTIVE</th>
                                    <th class="sort align-middle text-end pe-0" scope="col" data-sort="joined" style="width:19%;  min-width:200px;">JOINED</th>
                                </tr>
                                </thead>
                                <tbody class="list" id="members-table-body">
                                @foreach($this->salesRepresentative->wholesales_users as $customer)
                                    <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                        <td class="fs-9 align-middle ps-0 py-3">
                                            <div class="form-check mb-0 fs-8"><input class="form-check-input" type="checkbox" data-bulk-select-row='{"customer":{"avatar":"/team/25.webp","name":"Michael Jenkins"},"email":"jenkins@example.com","mobile":"+3026138829","city":"Philadelphia","lastActive":"12 hours ago","joined":"Oct 3, 8:30 AM"}' /></div>
                                        </td>
                                        <td class="customer align-middle white-space-nowrap"><a class="d-flex align-items-center text-body text-hover-1000" href="{{ route('backend.admin.customer_manager.wholesales.view', $customer->id) }}">
                                                <div class="avatar avatar-m"><img class="rounded-circle" src="{{ asset($customer->user->image) }}" alt="" /></div>
                                                <h6 class="mb-0 ms-3 fw-semibold">{{ $customer->business_name }}</h6>
                                            </a></td>
                                        <td class="email align-middle white-space-nowrap"><a class="fw-semibold" href="mailto:{{ $customer->business_email }}">{{ $customer->business_email }}</a></td>
                                        <td class="mobile_number align-middle white-space-nowrap"><a class="fw-bold text-body-emphasis" href="tel:{{ $customer->phone }}">{{ $customer->phone }}</a></td>
                                        <td class="last_active align-middle text-end white-space-nowrap text-body-tertiary">{{ (new \Carbon\Carbon($customer->user->last_seen))->diffForHumans() }}</td>
                                        <td class="joined align-middle white-space-nowrap text-body-tertiary text-end">{{ (new \Carbon\Carbon($customer->user->created_at))->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row align-items-center justify-content-between py-2 pe-0 fs-9">
                            <div class="col-auto d-flex">
                                <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body" data-list-info="data-list-info"></p><a class="fw-semibold" href="#!" data-list-view="*">View all<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a><a class="fw-semibold d-none" href="#!" data-list-view="less">View Less<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
                            </div>
                            <div class="col-auto d-flex"><button class="page-link" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
                                <ul class="mb-0 pagination"></ul><button class="page-link pe-0" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-between">
                <div id="members" data-list='{"valueNames":["customer","email","mobile_number","city","last_active","joined"],"page":10,"pagination":true}'>
                    <div class="row align-items-center justify-content-between g-3 mb-4">
                        <h2 class="text-bold text-body-emphasis mb-1">Order List</h2>
                        <div class="col col-auto">
                            <div class="search-box">
                                <form class="position-relative"><input class="form-control search-input search" type="search" placeholder="Search orders" aria-label="Search" />
                                    <span class="fas fa-search search-box-icon"></span>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 px-lg-6 mb-5 bg-body-emphasis border-y mt-2 position-relative top-1">
                        <div class="table-responsive scrollbar ms-n1 ps-1">
                            <table class="table table-sm fs-9 mb-0">
                                <thead>
                                <tr>
                                    <th class="white-space-nowrap fs-9 align-middle ps-0">
                                        <div class="form-check mb-0 fs-8"><input class="form-check-input" id="checkbox-bulk-members-select" type="checkbox" data-bulk-select='{"body":"members-table-body"}' /></div>
                                    </th>
                                    <th class="sort align-middle" scope="col" data-sort="customer" style="width:15%; min-width:200px;">CUSTOMER</th>
                                    <th class="sort align-middle" scope="col" data-sort="email" style="width:15%; min-width:200px;">EMAIL</th>
                                    <th class="sort align-middle pe-3" scope="col" data-sort="mobile_number" style="width:10%; min-width:200px;">ORDER ID</th>
                                    <th class="sort align-middle text-end" scope="col" data-sort="last_active" style="width:10%;">INVOICE NO.</th>
                                    <th class="sort align-middle text-end pe-0" scope="col" data-sort="joined" style="width:10%;  min-width:200px;">DATE</th>
                                    <th class="sort align-middle text-end pe-0" scope="col" data-sort="joined" style="width:10%;  min-width:200px;">TOTAL</th>
                                </tr>
                                </thead>
                                <tbody class="list" id="members-table-body">
                                    @foreach($this->salesRepresentative->orders as $order)
                                        <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                            <td class="fs-9 align-middle ps-0 py-3">
                                                <div class="form-check mb-0 fs-8"><input class="form-check-input" type="checkbox" data-bulk-select-row='{"customer":{"avatar":"/team/25.webp","name":"Michael Jenkins"},"email":"jenkins@example.com","mobile":"+3026138829","city":"Philadelphia","lastActive":"12 hours ago","joined":"Oct 3, 8:30 AM"}' /></div>
                                            </td>
                                            <td class="customer align-middle white-space-nowrap"><a class="d-flex align-items-center text-body text-hover-1000" href="{{ route('backend.admin.customer_manager.wholesales.view', $order->customer->id) }}">
                                                    <div class="avatar avatar-m"><img class="rounded-circle" src="{{ asset($order->customer->user->image) }}" alt="" /></div>
                                                    <h6 class="mb-0 ms-3 fw-semibold">{{ $order->customer->business_name }}</h6>
                                                </a></td>
                                            <td class="email align-middle white-space-nowrap"><a class="fw-semibold" href="{{ route('backend.admin.customer_manager.wholesales.view', $order->customer->id) }}">{{ $order->customer->business_name }}</a></td>
                                            <td class="email align-middle white-space-nowrap"><a class="fw-semibold" href="mailto:{{ $order->customer->business_email }}">{{ $order->customer->business_email }}</a></td>
                                            <td class="mobile_number align-middle white-space-nowrap"><a class="fw-bold text-body-emphasis" href="{{ route('backend.admin.order.view', $order->id) }}">#{{ $order->order_no }}</a></td>
                                            <td class="last_active align-middle text-end white-space-nowrap text-body-tertiary">{{ $order->order_date }}</td>
                                            <td class="joined align-middle white-space-nowrap text-body-tertiary text-end">{{ money($order->total) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row align-items-center justify-content-between py-2 pe-0 fs-9">
                            <div class="col-auto d-flex">
                                <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body" data-list-info="data-list-info"></p><a class="fw-semibold" href="#!" data-list-view="*">View all<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a><a class="fw-semibold d-none" href="#!" data-list-view="less">View Less<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
                            </div>
                            <div class="col-auto d-flex"><button class="page-link" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
                                <ul class="mb-0 pagination"></ul><button class="page-link pe-0" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @livewire("backend.component.salesrep.addcustomer", ['salesRepresentative' => $this->salesRepresentative], "add-customer-to-sales-rep"))
    @livewire("backend.component.resetpassword", ['user' => $this->salesRepresentative->user], "reset-sales-rep-password"))
</div>
