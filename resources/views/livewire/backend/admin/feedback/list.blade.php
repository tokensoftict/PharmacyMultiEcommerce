<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Feedback;
use App\Exports\FeedbackExport;
use Maatwebsite\Excel\Facades\Excel;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $rating = '';
    public $store = '';
    public $department = '';
    public $type = '';
    public $staff_id = '';
    public $fromDate = '';
    public $toDate = '';
    public $staffs_list = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'rating' => ['except' => ''],
        'store' => ['except' => ''],
        'department' => ['except' => ''],
        'type' => ['except' => ''],
        'staff_id' => ['except' => ''],
    ];

    public function mount()
    {
        $this->staffs_list = \App\Models\Staff::where('status', true)->orderBy('name')->get();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'rating', 'store', 'department', 'type', 'staff_id', 'fromDate', 'toDate'])) {
            $this->resetPage();
        }
    }

    public function export()
    {
        $feedbacks = $this->getFeedbacksQuery()->get();
        return Excel::download(new FeedbackExport($feedbacks), 'feedbacks-' . now()->format('Y-m-d') . '.xlsx');
    }

    protected function getFeedbacksQuery()
    {
        return Feedback::with('staff')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('full_name', 'like', '%' . $this->search . '%')
                        ->orWhere('phone_number', 'like', '%' . $this->search . '%')
                        ->orWhere('invoice_number', 'like', '%' . $this->search . '%')
                        ->orWhere('feedback', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->rating, fn($q) => $q->where('rating', $this->rating))
            ->when($this->store, fn($q) => $q->where('store', $this->store))
            ->when($this->department, fn($q) => $q->where('department', $this->department))
            ->when($this->type, fn($q) => $q->where('feedback_type', $this->type))
            ->when($this->staff_id, fn($q) => $q->where('staff_id', $this->staff_id))
            ->when($this->fromDate, fn($q) => $q->whereDate('created_at', '>=', $this->fromDate))
            ->when($this->toDate, fn($q) => $q->whereDate('created_at', '<=', $this->toDate))
            ->latest();
    }

    public function rendering($view, $data)
    {
        return $view->with([
            'feedbacks' => $this->getFeedbacksQuery()->paginate(15)
        ]);
    }
}; ?>

@push('breadcrumbs')
    <li class="breadcrumb-item"><a
            href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix . 'admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a
            href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix . 'backend.admin.feedback.dashboard') }}">Feedback
            Dashboard</a></li>
    <li class="breadcrumb-item active">Feedback List</li>
@endpush

<div>
    <div class="row align-items-center justify-content-between g-3 mb-4">
        <div class="col-auto">
            <h2 class="mb-0">Customer Feedback Reports</h2>
        </div>
        <div class="col-auto">
            <button wire:click="export" class="btn btn-phoenix-success">
                <span class="fas fa-file-excel me-2"></span>Export to Excel
            </button>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="search-box">
                        <div class="position-relative">
                            <input wire:model.live.debounce.300ms="search" class="form-control search-input"
                                type="search" placeholder="Search by name, phone, invoice..." />
                            <span class="fas fa-search search-box-icon text-body-quaternary"></span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <select wire:model.live="rating" class="form-select">
                        <option value="">All Ratings</option>
                        <option value="5">5 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="2">2 Stars</option>
                        <option value="1">1 Star</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select wire:model.live="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="Positive">Positive</option>
                        <option value="Negative">Negative</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select wire:model.live="store" class="form-select">
                        <option value="">All Stores</option>
                        <option value="Physical">Physical</option>
                        <option value="Online">Online</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select wire:model.live="department" class="form-select">
                        <option value="">All Depts</option>
                        <option value="Wholesales">Wholesales</option>
                        <option value="Retail">Retail</option>
                    </select>
                </div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-6 col-md-2">
                    <label class="form-label fs-10 mb-0">From Date</label>
                    <input type="date" wire:model.live="fromDate" class="form-control form-control-sm" />
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label fs-10 mb-0">To Date</label>
                    <input type="date" wire:model.live="toDate" class="form-control form-control-sm" />
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fs-10 mb-0">Filter by Staff</label>
                    <select wire:model.live="staff_id" class="form-select form-select-sm">
                        <option value="">All Staff</option>
                        @foreach($staffs_list as $s)
                            <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->department }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive scrollbar">
                <table class="table table-sm fs-9 mb-0">
                    <thead>
                        <tr>
                            <th class="sort border-top ps-0">Customer</th>
                            <th class="sort border-top">Contact</th>
                            <th class="sort border-top">Store/Dept</th>
                            <th class="sort border-top">Invoice</th>
                            <th class="sort border-top">Rating</th>
                            <th class="sort border-top">Type</th>
                            <th class="sort border-top" style="max-width: 300px;">Feedback</th>
                            <th class="sort border-top text-end pe-0">Submitted At</th>
                        </tr>
                    </thead>
                    <tbody class="list">
                        @forelse($feedbacks as $fb)
                            <tr class="hover-actions-trigger btn-reveal-trigger">
                                <td class="align-middle white-space-nowrap ps-0 py-3">
                                    <div class="fw-bold">{{ $fb->full_name }}</div>
                                    @if($fb->staff)
                                        <div class="fs-10 text-body-tertiary">Staff: {{ $fb->staff->name }}</div>
                                    @endif
                                </td>
                                <td class="align-middle white-space-nowrap py-3">
                                    <a class="text-body-emphasis fw-semibold"
                                        href="tel:{{ $fb->phone_number }}">{{ $fb->phone_number }}</a>
                                </td>
                                <td class="align-middle white-space-nowrap py-3">
                                    <span class="badge badge-phoenix fs-10 badge-phoenix-info me-1">{{ $fb->store }}</span>
                                    <span
                                        class="badge badge-phoenix fs-10 badge-phoenix-secondary">{{ $fb->department }}</span>
                                </td>
                                <td class="align-middle white-space-nowrap py-3">
                                    <span class="fw-semibold text-primary">#{{ $fb->invoice_number }}</span>
                                </td>
                                <td class="align-middle py-3">
                                    <div class="d-flex align-items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span
                                                class="fas fa-star {{ $i <= $fb->rating ? 'text-warning' : 'text-body-quaternary' }} fs-11"></span>
                                        @endfor
                                    </div>
                                </td>
                                <td class="align-middle py-3">
                                    <span
                                        class="badge badge-phoenix fs-10 {{ $fb->feedback_type == 'Positive' ? 'badge-phoenix-success' : 'badge-phoenix-danger' }}">
                                        {{ $fb->feedback_type }}
                                    </span>
                                </td>
                                <td class="align-middle py-3" style="max-width: 300px;">
                                    <p class="mb-0 text-body-tertiary lh-sm">{{ $fb->feedback }}</p>
                                </td>
                                <td class="align-middle text-end white-space-nowrap py-3 pe-0 text-body-tertiary">
                                    {{ $fb->created_at->format('M d, Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <h4 class="text-body-quaternary">No feedback records found.</h4>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $feedbacks->links() }}
            </div>
        </div>
    </div>
</div>