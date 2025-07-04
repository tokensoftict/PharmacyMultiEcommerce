<?php

namespace App\Services;

use App\Classes\ApplicationEnvironment;
use App\Classes\Settings;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get sales data by month.
     *
     * @param int|null $year If null, defaults to current year
     * @return array
     */
    public final function getMonthlySalesData(int $year = null): array
    {
        $year = $year ?? now()->year;

        $sales = Order::select(
            DB::raw('MONTH(order_date) as month'),
            DB::raw('SUM(total) as total')
        )
            ->whereYear('order_date', $year)
            ->where("orders.app_id", ApplicationEnvironment::$model_id)
            ->whereIn('status_id', Settings::Completed())
            ->groupBy(DB::raw('MONTH(order_date)'))
            ->orderBy(DB::raw('MONTH(order_date)'))
            ->pluck('total', 'month');

        // Prepare data for all 12 months (fill missing months with 0)
        $labels = [];
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create()->month($i)->format('M'); // e.g. Jan, Feb
            $data[] = round($sales[$i] ?? 0, 2);
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * @return array[]
     */
    public final function getLast7DaysStatusBreakdown(): array
    {
        $orderStatus = array_merge(Settings::InProgress(), Settings::Completed());

        $from = now()->subDays(6)->startOfDay();
        $to = now()->endOfDay();

        $orders = Order::selectRaw('DATE(order_date) as date, status_id, COUNT(*) as count, SUM(total) as total')
            ->whereBetween('order_date', [$from, $to])
            ->where("orders.app_id", ApplicationEnvironment::$model_id)
            ->whereIn('status_id', $orderStatus)
            ->groupBy(DB::raw('DATE(order_date)'), 'status_id')
            ->get();

        $labels = [];
        $completedData = [];
        $pendingData = [];

        $completedCount = 0;
        $pendingCount = 0;
        $completedSum = 0;
        $pendingSum = 0;

        for ($i = 0; $i < 7; $i++) {
            $date = now()->subDays(6 - $i)->startOfDay();
            $labels[] = $date->format('D');

            $completed = $orders->firstWhere(fn ($o) =>
                $o->date === $date->toDateString() &&  in_array($o->status_id, Settings::Completed())
            );

            $pending = $orders->firstWhere(fn ($o) =>
                $o->date === $date->toDateString() && in_array($o->status_id, Settings::InProgress())
            );

            $completedData[] = $completed?->count ?? 0;
            $pendingData[] = $pending?->count ?? 0;

            $completedCount += $completed?->count ?? 0;
            $pendingCount += $pending?->count ?? 0;
            $completedSum += $completed?->total ?? 0;
            $pendingSum += $pending?->total ?? 0;
        }

        $totalOrders = $completedCount + $pendingCount;
        $totalAmount = round($completedSum + $pendingSum, 2);

        $percentCompleted = $totalOrders > 0 ? round(($completedCount / $totalOrders) * 100, 2) : 0;
        $percentPending = $totalOrders > 0 ? round(($pendingCount / $totalOrders) * 100, 2) : 0;

        $percentageDifference = round(abs($percentCompleted - $percentPending), 2);

        return [
            'labels' => $labels,
            'completed' => $completedData,
            'pending' => $pendingData,
            'percentages' => [
                'completed' => $percentCompleted,
                'pending' => $percentPending,
                'difference' => $percentageDifference
            ],
            'totals' => [
                'orders' => $totalOrders,
                'amount' => $totalAmount
            ]
        ];
    }


    /**
     * @return array
     */
    public final function getNewCustomersComparisonWithStats(): array
    {
        $today = now();
        $startOfWeek = $today->copy()->startOfWeek(); // Monday
        $endOfWeek = $today->copy()->endOfWeek();     // Sunday

        // Current month - this week
        $thisWeekDates = collect();
        $thisMonthCounts = [];
        $thisMonthTotal = 0;

        for ($i = 0; $i < 7; $i++) {
            $day = $startOfWeek->copy()->addDays($i);
            $thisWeekDates->push($day->toDateString());

            $count = User::whereDate('created_at', $day)->count();
            $thisMonthCounts[] = $count;
            $thisMonthTotal += $count;
        }

        // Last month - same week
        $startOfWeekLastMonth = $startOfWeek->copy()->subMonth();
        $lastMonthCounts = [];
        $lastMonthTotal = 0;

        for ($i = 0; $i < 7; $i++) {
            $day = $startOfWeekLastMonth->copy()->addDays($i);
            $count = User::whereDate('created_at', $day)->count();
            $lastMonthCounts[] = $count;
            $lastMonthTotal += $count;
        }

        // Calculate percentage difference
        $percentageDifference = 0;
        $prefix = '';

        if ($lastMonthTotal > 0) {
            $rawDifference = (($thisMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100;
            $percentageDifference = round(abs($rawDifference), 2);
            $prefix = $rawDifference > 0 ? '+' : ($rawDifference < 0 ? '-' : '');
        } elseif ($thisMonthTotal > 0) {
            $percentageDifference = 100;
            $prefix = '+';
        }

// Final return
        return [
            'dates' => $thisWeekDates->toArray(),
            'this_month' => $thisMonthCounts,
            'last_month' => $lastMonthCounts,
            'totals' => [
                'this_month' => $thisMonthTotal,
                'last_month' => $lastMonthTotal,
            ],
            'percentage_difference' => $prefix . $percentageDifference . '%'
        ];
    }
}
