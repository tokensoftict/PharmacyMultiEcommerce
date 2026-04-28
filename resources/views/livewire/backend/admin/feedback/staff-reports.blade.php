<?php

use Livewire\Volt\Component;
use App\Models\Staff;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $sortField = 'feedbacks_count';
    public $sortDirection = 'desc';
    public $filter = 'all_time';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedFilter()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function getStaffQuery()
    {
        $dateConstraint = function (Builder $query) {
            if ($this->filter === 'daily') {
                $query->whereDate('created_at', Carbon::today());
            } elseif ($this->filter === 'weekly') {
                $query->where('created_at', '>=', Carbon::now()->startOfWeek());
            } elseif ($this->filter === 'monthly') {
                $query->where('created_at', '>=', Carbon::now()->startOfMonth());
            }
        };

        return Staff::where('status', true)
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('department', 'like', '%' . $this->search . '%');
            })
            ->withCount(['feedbacks' => $dateConstraint])
            ->withCount(['feedbacks as positive_count' => function (Builder $query) use ($dateConstraint) {
                $dateConstraint($query);
                $query->where('feedback_type', 'Positive');
            }])
            ->withCount(['feedbacks as negative_count' => function (Builder $query) use ($dateConstraint) {
                $dateConstraint($query);
                $query->where('feedback_type', 'Negative');
            }])
            ->withAvg(['feedbacks as avg_rating' => $dateConstraint], 'rating')
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function rendering($view, $data)
    {
        return $view->with([
            'staffs' => $this->getStaffQuery()->paginate(15)
        ]);
    }
}; ?>

@push('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix . 'admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix . 'backend.admin.feedback.dashboard') }}">Feedback Dashboard</a></li>
    <li class="breadcrumb-item active">Staff Ranking Summary</li>
@endpush

<div>
    <div class="row align-items-center justify-content-between g-3 mb-4">
        <div class="col-auto">
            <h2 class="mb-0">Staff Ranking Summary</h2>
            <p class="text-body-tertiary lh-sm mb-0">Overview of all staff performance metrics. Currently showing <strong>{{ $sortField == 'avg_rating' ? ($sortDirection == 'asc' ? 'Low-Performing' : 'High-Performing') : 'All' }}</strong> staff first.</p>
        </div>
        <div class="col-auto d-flex gap-2">
            <select wire:model.live="filter" class="form-select form-select-sm" style="max-width: 200px;">
                <option value="all_time">All Time</option>
                <option value="daily">Daily (Today)</option>
                <option value="weekly">Weekly (This Week)</option>
                <option value="monthly">Monthly (This Month)</option>
            </select>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="search-box">
                        <div class="position-relative">
                            <input wire:model.live.debounce.300ms="search" class="form-control search-input" type="search" placeholder="Search staff name or department..." />
                            <span class="fas fa-search search-box-icon text-body-quaternary"></span>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <button wire:click="sortBy('avg_rating')" class="btn btn-sm {{ $sortField == 'avg_rating' ? 'btn-phoenix-primary' : 'btn-outline-primary' }}">
                        Sort by Rating {{ $sortField == 'avg_rating' ? ($sortDirection == 'asc' ? '↓' : '↑') : '' }}
                    </button>
                    <button wire:click="sortBy('feedbacks_count')" class="btn btn-sm {{ $sortField == 'feedbacks_count' ? 'btn-phoenix-primary' : 'btn-outline-primary' }}">
                        Sort by Volume {{ $sortField == 'feedbacks_count' ? ($sortDirection == 'asc' ? '↓' : '↑') : '' }}
                    </button>
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
                            <th class="border-top ps-0 cursor-pointer" wire:click="sortBy('name')">
                                Staff Member {!! $sortField === 'name' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' !!}
                            </th>
                            <th class="border-top cursor-pointer" wire:click="sortBy('department')">
                                Department {!! $sortField === 'department' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' !!}
                            </th>
                            <th class="border-top cursor-pointer" wire:click="sortBy('feedbacks_count')">
                                Total Feedback {!! $sortField === 'feedbacks_count' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' !!}
                            </th>
                            <th class="border-top cursor-pointer text-success" wire:click="sortBy('positive_count')">
                                Positive Count {!! $sortField === 'positive_count' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' !!}
                            </th>
                            <th class="border-top cursor-pointer text-danger" wire:click="sortBy('negative_count')">
                                Negative Count {!! $sortField === 'negative_count' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' !!}
                            </th>
                            <th class="border-top cursor-pointer" wire:click="sortBy('avg_rating')">
                                Average Rating {!! $sortField === 'avg_rating' ? ($sortDirection === 'asc' ? '↑' : '↓') : '' !!}
                            </th>
                            <th class="border-top text-end pe-0">Action</th>
                        </tr>
                    </thead>
                    <tbody class="list">
                        @forelse($staffs as $staff)
                            @php
                                $perfClass = 'badge-phoenix-success';
                                $rowClass = '';
                                if ($staff->avg_rating > 0) {
                                    if ($staff->avg_rating < 3.0) {
                                        $perfClass = 'badge-phoenix-danger';
                                        $rowClass = 'bg-danger-subtle';
                                    } elseif ($staff->avg_rating < 4.0) {
                                        $perfClass = 'badge-phoenix-warning';
                                    }
                                }
                            @endphp
                            <tr class="hover-actions-trigger btn-reveal-trigger {{ $rowClass }}">
                                <td class="align-middle white-space-nowrap ps-0 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-l me-2">
                                            <div class="avatar-name rounded-circle"><span>{{ substr($staff->name, 0, 1) }}</span></div>
                                        </div>
                                        <h6 class="mb-0 text-body-emphasis">{{ $staff->name }}</h6>
                                    </div>
                                </td>
                                <td class="align-middle white-space-nowrap py-3 text-body-tertiary">
                                    {{ $staff->department }}
                                </td>
                                <td class="align-middle white-space-nowrap py-3">
                                    <span class="badge badge-phoenix fs-10 badge-phoenix-primary">{{ $staff->feedbacks_count }} Entries</span>
                                </td>
                                <td class="align-middle white-space-nowrap py-3">
                                    <span class="badge badge-phoenix fs-10 badge-phoenix-success">{{ $staff->positive_count }} Positive</span>
                                </td>
                                <td class="align-middle white-space-nowrap py-3">
                                    <span class="badge badge-phoenix fs-10 badge-phoenix-danger">{{ $staff->negative_count }} Negative</span>
                                </td>
                                <td class="align-middle py-3">
                                    <div class="d-flex align-items-center">
                                        <span class="badge {{ $perfClass }} me-2 fw-bold fs-9">{{ number_format($staff->avg_rating, 1) }}</span>
                                        <div class="d-none d-lg-block">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="fas fa-star {{ $i <= $staff->avg_rating ? 'text-warning' : 'text-body-quaternary' }} fs-11"></span>
                                            @endfor
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle text-end white-space-nowrap py-3 pe-0">
                                    <a href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix . 'backend.admin.feedback.dashboard', ['staff_id' => $staff->id]) }}" class="btn btn-sm btn-phoenix-primary">
                                        <span class="fas fa-chart-line me-1"></span>Analyze Performance
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <h4 class="text-body-quaternary">No staff records found.</h4>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $staffs->links() }}
            </div>
        </div>
    </div>
</div>
