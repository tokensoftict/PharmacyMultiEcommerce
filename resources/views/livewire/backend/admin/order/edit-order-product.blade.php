@section('pageHeaderTitle')
    Edit Order #{{ $this->order->order_id }}
@endsection

@push('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('backend.admin.order.list') }}">Order List</a></li>
    <li class="breadcrumb-item active">Edit Order #{{ $this->order->order_id }}</li>
@endpush
@script
<script>
    window.removeEventListener('refreshPage', refreshPage);

    function refreshPage() {
        setTimeout(function(){
            window.location.href = '{{ route('backend.admin.order.view', $this->order->id) }}'
        }, 1000 )
    }

    window.addEventListener('refreshPage', refreshPage);

</script>
@endscript


<div>
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
                                        <th class="sort align-middle text-center ps-4">Quantity</th>
                                        <th class="sort align-middle text-end ps-4">Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($this->order->order_products as $key=> $product)
                                        <tr>
                                            <td class="sort white-space-nowrap align-middle fs-10">{{ $loop->iteration }}</td>
                                            <td class="sort white-space-nowrap align-middle">{{ $product->name }}</td>
                                            <td class="sort align-middle text-end ps-4">{{ money($product->price) }}</td>
                                            <td class="sort align-middle text-end ps-4 col-2">
                                                <input type="number" wire:model="products.{{ $key }}.quantity"  class="form-control text-center form-control-sm">
                                            </td>
                                            <td class="sort align-middle text-end ps-4">{{ money($product->total) }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>

                                <button type="button" wire:target="saveChanges" wire:loading.attr="disabled" wire:click="saveChanges" class="btn btn-lg btn-primary mt-4 float-end">
                                    <span wire:loading.remove="saveChanges" class="fas fa-edit me-2"></span>
                                    <span wire:loading wire:target="saveChanges" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    Update Products
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
