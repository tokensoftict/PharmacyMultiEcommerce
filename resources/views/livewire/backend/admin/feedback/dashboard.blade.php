<?php

use Livewire\Volt\Component;
use App\Models\Feedback;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

new class extends Component {
new class extends Component {
    public $stats = [];
    public $trendData = [];
    public $sentimentData = [];
    public $storeData = [];
    public $recentFeedbacks = [];
    public $staffRanking = [];
    public $staff_id = null;
    public $selectedStaff = null;

    protected $queryString = ['staff_id'];

    public function mount()
    {
        if ($this->staff_id) {
            $this->selectedStaff = \App\Models\Staff::find($this->staff_id);
        }
        $this->loadAllData();
    }

    public function loadAllData()
    {
        $this->loadStats();
        $this->loadTrendData();
        $this->loadSentimentData();
        $this->loadStoreData();
        $this->loadRecentFeedbacks();
        if (!$this->staff_id) {
            $this->loadStaffRanking();
        }
    }

    public function loadStats()
    {
        $query = Feedback::query()->when($this->staff_id, fn($q) => $q->where('staff_id', $this->staff_id));
        
        $total = $query->count();
        $avgRating = (clone $query)->avg('rating') ?: 0;

        // NPS Calculation (1-5 scale)
        $promoters = (clone $query)->where('rating', 5)->count();
        $detractors = (clone $query)->where('rating', '<=', 3)->count();
        $nps = $total > 0 ? (($promoters - $detractors) / $total) * 100 : 0;

        $this->stats = [
            'total' => $total,
            'avg_rating' => round($avgRating, 1),
            'nps' => round($nps, 0),
            'trend_up' => (clone $query)->where('created_at', '>=', Carbon::now()->subDays(7))->count(),
        ];
    }

    public function loadTrendData()
    {
        $days = 30;
        $trend = Feedback::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as aggregate'))
            ->when($this->staff_id, fn($q) => $q->where('staff_id', $this->staff_id))
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $values = [];

        for ($i = $days; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($date)->format('M d');
            $match = $trend->firstWhere('date', $date);
            $values[] = $match ? $match->aggregate : 0;
        }

        $this->trendData = [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    public function loadSentimentData()
    {
        $query = Feedback::query()->when($this->staff_id, fn($q) => $q->where('staff_id', $this->staff_id));
        
        $positive = (clone $query)->where('feedback_type', 'Positive')->count();
        $negative = (clone $query)->where('feedback_type', 'Negative')->count();

        $this->sentimentData = [
            ['value' => $positive, 'name' => 'Positive'],
            ['value' => $negative, 'name' => 'Negative'],
        ];
    }

    public function loadStoreData()
    {
        $this->storeData = Feedback::select('store', DB::raw('count(*) as aggregate'))
            ->when($this->staff_id, fn($q) => $q->where('staff_id', $this->staff_id))
            ->groupBy('store')
            ->get()
            ->map(function ($item) {
                return ['value' => $item->aggregate, 'name' => $item->store];
            })->toArray();
    }

    public function loadRecentFeedbacks()
    {
        $this->recentFeedbacks = Feedback::with('staff')
            ->when($this->staff_id, fn($q) => $q->where('staff_id', $this->staff_id))
            ->latest()
            ->take(5)
            ->get();
    }

    public function loadStaffRanking()
    {
        $this->staffRanking = \App\Models\Staff::where('status', true)
            ->withCount('feedbacks')
            ->withAvg('feedbacks as avg_rating', 'rating')
            ->orderByDesc('feedbacks_count')
            ->take(5)
            ->get();
    }
}; ?>

@push('breadcrumbs')
    <li class="breadcrumb-item"><a
            href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix . 'admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Feedback Dashboard</li>
@endpush

@push('js')
    <script src="{{ asset('vendor/echarts/echarts.min.js') }}"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Trend Chart
            const trendChart = echarts.init(document.getElementById('trend-chart'));
            trendChart.setOption({
                tooltip: { trigger: 'axis' },
                xAxis: { type: 'category', data: @json($trendData['labels']) },
                yAxis: { type: 'value' },
                series: [{
                    data: @json($trendData['values']),
                    type: 'line',
                    smooth: true,
                    areaStyle: { opacity: 0.1 },
                    itemStyle: { color: '#3874ff' }
                }],
                grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true }
            });

            // Sentiment Chart
            const sentimentChart = echarts.init(document.getElementById('sentiment-chart'));
            sentimentChart.setOption({
                tooltip: { trigger: 'item' },
                legend: { bottom: '0', left: 'center' },
                series: [{
                    type: 'pie',
                    radius: ['40%', '70%'],
                    avoidLabelOverlap: false,
                    itemStyle: { borderRadius: 10, borderColor: '#fff', borderWidth: 2 },
                    label: { show: false },
                    data: @json($sentimentData),
                    color: ['#00d27a', '#f5803e']
                }]
            });

            // Store Distribution
            const storeChart = echarts.init(document.getElementById('store-chart'));
            storeChart.setOption({
                tooltip: { trigger: 'item' },
                series: [{
                    type: 'pie',
                    radius: '70%',
                    data: @json($storeData),
                    emphasis: {
                        itemStyle: { shadowBlur: 10, shadowOffsetX: 0, shadowColor: 'rgba(0, 0, 0, 0.5)' }
                    }
                }]
            });

            window.addEventListener('resize', () => {
                trendChart.resize();
                sentimentChart.resize();
                storeChart.resize();
            });
        });
    </script>
@endpush

<div class="pb-9">
    <div class="row align-items-center justify-content-between g-3 mb-4">
        <div class="col-auto">
            <h2 class="mb-0">
                @if($selectedStaff)
                    Performance Analysis: {{ $selectedStaff->name }}
                @else
                    Extreme Feedback Dashboard
                @endif
            </h2>
            <p class="text-body-tertiary lh-sm mb-0">
                @if($selectedStaff)
                    Deep-dive into performance metrics for {{ $selectedStaff->name }} ({{ $selectedStaff->department }}).
                @else
                    Real-time customer feedback insights for quick decision making.
                @endif
            </p>
        </div>
        <div class="col-auto d-flex gap-2">
            @if($selectedStaff)
                <a href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix . 'backend.admin.feedback.dashboard') }}" class="btn btn-phoenix-secondary">
                    <span class="fas fa-globe me-2"></span>Back to Global View
                </a>
            @endif
            <a href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix . 'backend.admin.feedback.list') }}"
                class="btn btn-phoenix-primary">
                <span class="fas fa-list me-2"></span>View All Feedbacks
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card h-100 bg-primary-subtle">
                <div class="card-body">
                    <div class="d-flex d-sm-block justify-content-between">
                        <h4 class="mb-1 text-primary">Total Feedback</h4>
                        <div class="d-flex align-items-center">
                            <h3 class="mb-0 text-primary-emphasis">{{ $stats['total'] }}</h3>
                            <span class="badge badge-phoenix badge-phoenix-primary ms-2">+{{ $stats['trend_up'] }} this
                                week</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card h-100 bg-success-subtle">
                <div class="card-body">
                    <div class="d-flex d-sm-block justify-content-between">
                        <h4 class="mb-1 text-success">Avg Rating</h4>
                        <div class="d-flex align-items-center">
                            <h3 class="mb-0 text-success-emphasis">{{ $stats['avg_rating'] }} / 5</h3>
                            <div class="ms-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <span
                                        class="fas fa-star {{ $i <= $stats['avg_rating'] ? 'text-warning' : 'text-body-quaternary' }} fs-10"></span>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card h-100 bg-info-subtle">
                <div class="card-body">
                    <div class="d-flex d-sm-block justify-content-between">
                        <h4 class="mb-1 text-info">NPS Score</h4>
                        <div class="d-flex align-items-center">
                            <h2 class="mb-0 text-info-emphasis">{{ $stats['nps'] }}</h2>
                            <span
                                class="ms-2 fw-bold {{ $stats['nps'] > 50 ? 'text-success' : ($stats['nps'] > 0 ? 'text-info' : 'text-danger') }}">
                                {{ $stats['nps'] > 50 ? 'EXCELLENT' : ($stats['nps'] > 0 ? 'GOOD' : 'CRITICAL') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card h-100 bg-warning-subtle">
                <div class="card-body">
                    <div class="d-flex d-sm-block justify-content-between">
                        <h4 class="mb-1 text-warning-emphasis">Positive Rate</h4>
                        <div class="d-flex align-items-center">
                            @php
                                $totalPos = collect($sentimentData)->firstWhere('name', 'Positive')['value'] ?? 0;
                                $rate = $stats['total'] > 0 ? round(($totalPos / $stats['total']) * 100) : 0;
                            @endphp
                            <h3 class="mb-0">{{ $rate }}%</h3>
                            <div class="progress ms-2 flex-1" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $rate }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-xl-8">
            <div class="card h-100">
                <div class="card-body">
                    <h3>Feedback Volume Trend (Last 30 Days)</h3>
                    <div id="trend-chart" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <h3>Sentiment Mix</h3>
                    <div id="sentiment-chart" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-lg-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h3>Store Distribution</h3>
                    <div id="store-chart" style="min-height: 250px;"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-8">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3>Latest Feedback</h3>
                        <a href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix . 'backend.admin.feedback.list') }}"
                            class="btn btn-link p-0">View all</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm fs-9 mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-0">Customer</th>
                                    <th>Rating</th>
                                    <th>Type</th>
                                    <th>Feedback</th>
                                    <th class="text-end pe-0">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentFeedbacks as $fb)
                                    <tr>
                                        <td class="ps-0 border-0 py-3">
                                            <div class="fw-bold">{{ $fb->full_name }}</div>
                                            <div class="fs-10 text-body-tertiary">{{ $fb->invoice_number }}</div>
                                        </td>
                                        <td class="border-0 py-3">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span
                                                    class="fas fa-star {{ $i <= $fb->rating ? 'text-warning' : 'text-body-quaternary' }} fs-11"></span>
                                            @endfor
                                        </td>
                                        <td class="border-0 py-3">
                                            <span
                                                class="badge badge-phoenix fs-10 {{ $fb->feedback_type == 'Positive' ? 'badge-phoenix-success' : 'badge-phoenix-danger' }}">
                                                {{ $fb->feedback_type }}
                                            </span>
                                        </td>
                                        <td class="border-0 py-3">
                                            <div class="text-truncate" style="max-width: 200px;"
                                                title="{{ $fb->feedback }}">
                                                {{ $fb->feedback }}
                                            </div>
                                        </td>
                                        <td class="text-end border-0 py-3 pe-0 text-body-tertiary">
                                            {{ $fb->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!$staff_id && count($staffRanking) > 0)
    <div class="row g-3 mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h3>Top Performing Staff</h3>
                    <div class="table-responsive">
                        <table class="table table-sm fs-9 mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-0">Staff Member</th>
                                    <th>Department</th>
                                    <th>Feedback Count</th>
                                    <th>Avg Rating</th>
                                    <th class="text-end pe-0">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($staffRanking as $staff)
                                    <tr>
                                        <td class="ps-0 py-3">
                                            <div class="fw-bold">{{ $staff->name }}</div>
                                        </td>
                                        <td class="py-3">{{ $staff->department }}</td>
                                        <td class="py-3">
                                            <span class="badge badge-phoenix badge-phoenix-primary">{{ $staff->feedbacks_count }}</span>
                                        </td>
                                        <td class="py-3">
                                            <div class="d-flex align-items-center">
                                                <span class="fw-bold me-2">{{ number_format($staff->avg_rating, 1) }}</span>
                                                @for($i = 1; $i <= 5; $i++)
                                                    <span class="fas fa-star {{ $i <= $staff->avg_rating ? 'text-warning' : 'text-body-quaternary' }} fs-11"></span>
                                                @endfor
                                            </div>
                                        </td>
                                        <td class="text-end py-3 pe-0">
                                            <a href="{{ route(\App\Classes\ApplicationEnvironment::$storePrefix . 'backend.admin.feedback.dashboard', ['staff_id' => $staff->id]) }}" class="btn btn-sm btn-phoenix-primary">
                                                Analyze Performance
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>