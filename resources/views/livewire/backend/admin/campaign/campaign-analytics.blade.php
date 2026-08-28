<div class="container-fluid py-4">
    <!-- Top Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Campaign Analytics: {{ $campaign->name }}</h1>
            <p class="text-muted small mb-0">
                Trigger: <span class="badge bg-secondary">{{ $campaign->trigger_event }}</span> |
                Status: <span class="badge bg-{{ $campaign->status === 'active' ? 'success' : 'warning' }}">{{ ucfirst($campaign->status) }}</span>
            </p>
        </div>
        <div class="d-flex gap-2">
            @if($campaign->status === 'active')
                <button wire:click="pauseCampaign" class="btn btn-warning btn-sm">
                    <i class="fa fa-pause me-1"></i> Pause Campaign
                </button>
            @else
                <button wire:click="activateCampaign" class="btn btn-success btn-sm">
                    <i class="fa fa-play me-1"></i> Activate Campaign
                </button>
            @endif
            <a href="{{ route('backend.admin.campaign.edit', $campaign->id) }}" class="btn btn-outline-primary btn-sm">
                <i class="fa fa-pencil me-1"></i> Edit Setup
            </a>
            <a href="{{ route('backend.admin.campaign.list') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <!-- Stat Cards Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card shadow-sm border-0 border-start border-primary border-4 rounded-3 p-3">
                <div class="text-muted small fw-bold text-uppercase">Impressions</div>
                <div class="h3 font-weight-bold text-primary mb-0 mt-1">{{ number_format($stats['total_impressions']) }}</div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card shadow-sm border-0 border-start border-info border-4 rounded-3 p-3">
                <div class="text-muted small fw-bold text-uppercase">Clicks</div>
                <div class="h3 font-weight-bold text-info mb-0 mt-1">{{ number_format($stats['total_clicks']) }}</div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card shadow-sm border-0 border-start border-success border-4 rounded-3 p-3">
                <div class="text-muted small fw-bold text-uppercase">Click-Through Rate</div>
                <div class="h3 font-weight-bold text-success mb-0 mt-1">{{ $stats['ctr'] }}%</div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card shadow-sm border-0 border-start border-warning border-4 rounded-3 p-3">
                <div class="text-muted small fw-bold text-uppercase">Dismissals</div>
                <div class="h3 font-weight-bold text-warning mb-0 mt-1">{{ number_format($stats['total_dismissals']) }}</div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card shadow-sm border-0 border-start border-purple border-4 rounded-3 p-3">
                <div class="text-muted small fw-bold text-uppercase">Push Notifications Sent</div>
                <div class="h3 font-weight-bold text-purple mb-0 mt-1">{{ number_format($stats['total_push_sent']) }}</div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card shadow-sm border-0 border-start border-dark border-4 rounded-3 p-3">
                <div class="text-muted small fw-bold text-uppercase">Conversions</div>
                <div class="h3 font-weight-bold text-dark mb-0 mt-1">{{ number_format($stats['total_conversions']) }}</div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Log Table -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-history me-2"></i>Recent Activity Stream</h6>
            <span class="badge bg-light text-dark">Real-time engagement log</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase small text-muted">
                    <tr>
                        <th>Date & Time</th>
                        <th>User</th>
                        <th>Event Type</th>
                        <th>Channel</th>
                        <th>Attribution</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentActivities as $act)
                        <tr>
                            <td class="small">{{ $act->created_at->format('M d, Y H:i:s') }}</td>
                            <td>
                                <div class="fw-bold">{{ $act->user?->name ?? 'User #' . $act->user_id }}</div>
                                <div class="text-muted small">{{ $act->user?->email }}</div>
                            </td>
                            <td>
                                @php
                                    $badge = match($act->event_type) {
                                        'impression' => 'bg-secondary',
                                        'clicked' => 'bg-info',
                                        'dismissed' => 'bg-warning',
                                        'converted' => 'bg-success',
                                        'push_sent' => 'bg-primary',
                                        'push_opened' => 'bg-dark',
                                        default => 'bg-light text-dark',
                                    };
                                @endphp
                                <span class="badge {{ $badge }}">{{ ucfirst($act->event_type) }}</span>
                            </td>
                            <td>
                                <span class="small text-muted">{{ strtoupper($act->channel ?? 'IN_APP') }}</span>
                            </td>
                            <td class="small text-muted">{{ $act->attributed_to ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No interactions logged yet for this campaign.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $recentActivities->links() }}
        </div>
    </div>
</div>
