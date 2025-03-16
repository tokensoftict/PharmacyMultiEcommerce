@push('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('backend.admin.push_notification.list') }}">Push Notification List</a></li>
    <li class="breadcrumb-item active">Push Notification #{{ $this->pushNotification->id }}</li>
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
            <h2 class="mb-0">Push Notification Report</h2>
        </div>
        <div class="col-auto">
            <div class="row g-3">
                @if($this->pushNotification->status == "APPROVED")
                    <div class="col-auto">
                        <button type="button" wire:click="cancel"  wire:confirm="Are you sure you want approved this push notification" class="btn btn-phoenix-danger"><span class="fas fa-close me-2"></span>Cancel</button>
                    </div>
                @endif
                @if($this->pushNotification->status == "DRAFT")
                    <div class="col-auto">
                        <button type="button" wire:click="approve" wire:confirm="Are you sure you want approved this push notification"  class="btn btn-phoenix-primary"><span class="fas fa-check me-2"></span>Approve</button>
                    </div>
                @endif
                @if($this->pushNotification->status == "DRAFT111")
                    <div class="col-auto">
                        <button type="button"  class="btn btn-phoenix-danger"><span class="fas fa-trash me-2"></span>Delete</button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-5 mt-2">
        <div class="col-12 col-xxl-4">
            <div class="row g-3 h-100">
                <div class="col-12 col-md-7 col-xxl-12">
                    <div class="card h-100 h-xxl-auto">
                        <div class="card-body d-flex flex-column justify-content-between pb-3">
                            <div class="d-flex align-items-center mb-3">
                                <h3 class="me-1">Push Notification Details</h3>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="text-body fs-9">Title :</p>
                                <p class="text-body-emphasis fw-semibold fs-9">{{ $this->pushNotification->title }}</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="text-body fs-9">Body :</p>
                                <p class="text-body-emphasis fw-semibold fs-9">{{ $this->pushNotification->body }}</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="text-body fs-9">Total Sent :</p>
                                <p class="text-body-emphasis fw-semibold fs-9">{{ $this->pushNotification->push_notification_customers()->where('status_id', status('Complete'))->count() }}</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="text-body fs-9">Total Open :</p>
                                <p class="text-body-emphasis fw-semibold fs-9">{{ $this->pushNotification->push_notification_customers()->where('status_id', status('Opened'))->count() }}</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="text-body fs-9">Customer Type :</p>
                                <p class="text-body-emphasis fw-semibold fs-9">{{ $this->pushNotification->customer_type->name ?? 'N/A' }}</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="text-body fs-9">Customer Group :</p>
                                <p class="text-body-emphasis fw-semibold fs-9">{{ $this->pushNotification->customer_group->name ?? 'N/A' }}</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="text-body fs-9">Status :</p>
                                <p class="text-body-emphasis fw-semibold fs-9">{!! label(ucwords($this->pushNotification->status), $this->notificationStatus[$this->pushNotification->status], 'lg') !!}</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p class="text-body fs-9">Created By :</p>
                                <p class="text-body-emphasis fw-semibold fs-9">{{ $this->pushNotification->user->firstname." ".$this->pushNotification->user->lastname }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xxl-8">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-between">
                        <div id="members">
                            <div class="row align-items-center justify-content-between g-3 mb-4">
                                <h2 class="text-bold text-body-emphasis mb-1">Customer List({{ $this->pushNotification->push_notification_customers->count() }})</h2>
                            </div>
                            <div class="px-4 px-lg-6 mb-5 bg-body-emphasis border-y mt-2 position-relative top-1">
                                <div class="table-responsive scrollbar ms-n1 ps-1">
                                    <table class="table table-sm fs-9 mb-0">
                                        <thead>
                                        <tr>
                                            <th class="sort align-middle" scope="col" data-sort="customer" style="width:15%; min-width:200px;">CUSTOMER</th>
                                            <th class="sort align-middle" scope="col" data-sort="email" style="width:15%; min-width:200px;">EMAIL</th>
                                            <th class="sort align-middle pe-3" scope="col" data-sort="mobile_number" style="width:20%; min-width:200px;">PHONE NUMBER</th>
                                            <th class="sort align-middle" scope="col" data-sort="email" style="width:15%; min-width:200px;">STATUS</th>
                                        </tr>
                                        </thead>
                                        <tbody class="list" id="members-table-body">
                                        @foreach($this->pushNotification->push_notification_customers as $customer)
                                            <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                                <td class="customer align-middle white-space-nowrap"><a class="d-flex align-items-center text-body text-hover-1000" href="{{ route('backend.admin.customer_manager.wholesales.view', $customer->customer->id) }}">
                                                        <div class="avatar avatar-m"><img class="rounded-circle" src="{{ asset($customer->customer->user->image) }}" alt="" /></div>
                                                        <h6 class="mb-0 ms-3 fw-semibold">{{ $customer->customer->business_name ??  $customer->customer->user->firstname." ".$customer->customer->user->lastname}}</h6>
                                                    </a>
                                                </td>
                                                <td class="email align-middle white-space-nowrap"><a class="fw-semibold" href="mailto:{{ $customer->customer->user->email }}">{{ $customer->customer->user->email }}</a></td>
                                                <td class="mobile_number align-middle white-space-nowrap"><a class="fw-bold text-body-emphasis" href="tel:{{ $customer->customer->user->phone }}">{{ $customer->customer->user->phone }}</a></td>
                                                <td>{!! showStatus($customer->status_id) !!}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <h3 class="me-1">Stock List({{ $this->pushNotification->stocks->count() }})</h3>
                    </div>
                    <div class="px-4 px-lg-6 mb-5 bg-body-emphasis border-y mt-2 position-relative top-1">
                        <div class="table-responsive scrollbar ms-n1 ps-1">
                            <table class="table table-sm fs-9 mb-0">
                                <thead>
                                <tr>
                                    <th class="sort align-middle" scope="col" data-sort="customer" style="width:15%; min-width:200px;">NAME</th>
                                    <th class="sort align-middle pe-3" scope="col" data-sort="mobile_number" style="width:20%; min-width:200px;">MANUFACTURER</th>
                                    <th class="sort align-middle" scope="col" data-sort="email" style="width:15%; min-width:200px;">CLASSIFICATION</th>
                                    <th class="sort align-middle" scope="col" data-sort="email" style="width:15%; min-width:200px;">PRICE</th>
                                </tr>
                                </thead>
                                <tbody class="list" id="members-table-body">
                                @foreach($this->pushNotification->stocks as $stock)
                                    <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                        <td class="customer align-middle white-space-nowrap"><a class="d-flex align-items-center text-body text-hover-1000" href="{{ route('backend.admin.stock_manager.view', $stock->stock->{\App\Classes\ApplicationEnvironment::$stock_model_string}->id) }}">
                                                <div class="avatar avatar-m"><img class="rounded-circle" src="{{ asset($stock->stock->product_image) }}" alt="" /></div>
                                                <h6 class="mb-0 ms-3 fw-semibold">{{ $stock->stock->name }}</h6>
                                            </a>
                                        </td>
                                        <td class="align-middle white-space-nowrap">{{ $stock->stock->manufacturer->name ?? "N/A" }}</td>
                                        <td class="align-middle white-space-nowrap">{{ $stock->stock->classification->name ?? "N/A" }}</td>
                                        <td>{{ money($stock->stock->{\App\Classes\ApplicationEnvironment::$stock_model_string}->price) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
