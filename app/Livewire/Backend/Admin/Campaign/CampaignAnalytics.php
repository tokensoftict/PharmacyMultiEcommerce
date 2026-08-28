<?php

namespace App\Livewire\Backend\Admin\Campaign;

use App\Models\Campaign;
use App\Models\CampaignActivity;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * CampaignAnalytics
 *
 * Livewire component showing detailed analytics for a single campaign:
 * impressions, clicks, dismissals, conversions, push sent, and timeline.
 */
class CampaignAnalytics extends Component
{
    use WithPagination;

    public Campaign $campaign;

    public function render()
    {
        $stats = [
            'total_impressions' => $this->campaign->total_impressions,
            'total_clicks'      => $this->campaign->total_clicks,
            'total_dismissals'  => $this->campaign->total_dismissals,
            'total_conversions' => $this->campaign->total_conversions,
            'total_push_sent'   => $this->campaign->total_push_sent,
            'ctr'               => $this->campaign->total_impressions > 0
                ? round(($this->campaign->total_clicks / $this->campaign->total_impressions) * 100, 1)
                : 0,
            'conversion_rate'   => $this->campaign->total_clicks > 0
                ? round(($this->campaign->total_conversions / $this->campaign->total_clicks) * 100, 1)
                : 0,
        ];

        $recentActivities = CampaignActivity::where('campaign_id', $this->campaign->id)
            ->with('user')
            ->latest()
            ->paginate(20);

        // Daily impressions for the last 30 days
        $dailyImpressions = CampaignActivity::where('campaign_id', $this->campaign->id)
            ->where('event_type', 'impression')
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('livewire.backend.admin.campaign.campaign-analytics', [
            'stats'            => $stats,
            'recentActivities' => $recentActivities,
            'dailyImpressions' => $dailyImpressions,
        ]);
    }

    public function activateCampaign(): void
    {
        $this->campaign->status = 'active';
        $this->campaign->save();
        $this->alert('success', 'Campaign activated.');
    }

    public function pauseCampaign(): void
    {
        $this->campaign->status = 'paused';
        $this->campaign->save();
        $this->alert('info', 'Campaign paused.');
    }

    public function archiveCampaign(): void
    {
        $this->campaign->status = 'archived';
        $this->campaign->save();
        $this->alert('warning', 'Campaign archived.');
    }
}
