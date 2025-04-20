<?php

namespace App\Services\Dashboard;

use App\Classes\ApplicationEnvironment;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DashboardMetricsService
{

    public User $user;
    public Order $order;
    public function __construct(User $user, Order $order)
    {
        $this->user = $user;
        $this->order = $order;
    }

    /**
     * @param array $firstRange
     * @param array $secondRange
     * @return array
     */

    /**
     * @param array $firstRange
     * @param array $secondRange
     * @return array
     */
    public final function compareLastSevenDaysNewCustomerMetrics(array $firstRange, array $secondRange) : array
    {
        $firstCount = $this->user->whereBetween('created_at', $firstRange)->count();
        $secondCount = $this->user->whereBetween('created_at', $secondRange)->count();

        return ['thisMonth' => $firstCount, 'lastMonth' => $secondCount];
    }

    /**
     * @param array $range
     * @return array
     */
    public final function compareLastMonthOrderMetrics(array $range) : array
    {
        $reports = [];
        $endDate = now()->endOfDay(); // today
        $startDate = now()->subDays(6)->startOfDay(); // 6 days ago to include today = 7 days

        $period = CarbonPeriod::create($startDate, $endDate);

        $dates = collect($period)->map(function ($date) {
            return $date->toDateString();
        });

        foreach ($dates as $date) {
            $completed =  $this->order->where('app_id', ApplicationEnvironment::$id)->where('order_date', $date)
                ->whereIn('status_id', [status('Dispatched'), status('Complete'), status('Paid')])->count();

            $pending =  $this->order->where('app_id', ApplicationEnvironment::$id)->where('order_date', $date)
                ->whereNotIn('status_id', [status('Dispatched'), status('Complete'), status('Paid')])->count();

            $reports[] = [
                'pending' => $pending,
                'completed' => $completed,
                'total' => $completed + $pending,
                'date' => carbonize($date->format('d F')),
                'pendingPercentage' => ceil($pending /100 *  ($completed + $pending)),
                'completedPercentage' => ceil($completed /100 *  ($completed + $pending))
            ];
        }

        return $reports;
    }

    /**
     * @return array
     */
    public final function computeThisYearSalesByMonth() : array
    {
        $year = now()->year;
        $reports = [];

        for ($month = 1; $month <= 12; $month++) {
            $start = Carbon::create($year, $month, 1)->startOfMonth();
            $end = Carbon::create($year, $month, 1)->endOfMonth();

            $total = $this->order->where('app_id', ApplicationEnvironment::$id)->whereBetween('order_date', [$start, $end])->sum('total');

            $reports[] = [
                'month' => $start->format('F'),
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
                'total' => $total
            ];
        }

        return $reports;
    }

}
