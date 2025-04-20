<?php

namespace App\Services\SalesRepresentative;

use App\Models\SalesRepresentative;
use App\Models\WholesalesUser;
use Carbon\Carbon;
use Illuminate\Support\Collection;


class ReportService
{
    public SalesRepresentative $salesRepresentative;
    public Carbon $from;
    public Carbon $to;
    public String $month;

    private WholesalesUser $wholesUsers;

    public function __construct(WholesalesUser $user)
    {
        $this->from = carbonize(date('Y-m-01'));
        $this->to = carbonize(date('Y-m-t'));
        $this->wholesUsers = $user;

        if(isset(request()->from) && isset(request()->to)){
            $this->from = request()->from;
            $this->to = request()->to;
        }

        if(date("m",strtotime($this->from)) == date("m",strtotime($this->to))){
            $this->month = date('F');
        }else{
            $this->month = date("F d Y",strtotime($this->from))." - ".date("F d Y",strtotime($this->to));
        }
    }

    /**
     * @param SalesRepresentative $salesRepresentative
     * @return void
     */
    public final function setSalesRepresentative(SalesRepresentative $salesRepresentative) : void
    {
        $this->salesRepresentative = $salesRepresentative;
    }

    /**
     * @return int
     */
    public final function geTotalOrderDispatchedCount() : int
    {
        return $this->salesRepresentative->orders()
            ->whereIn('status_id', [status('Paid'), status('Dispatched'), status('Complete')])
            ->whereBetween('order_date',[$this->from,$this->to])
            ->count();
    }

    /**
     * @return int|float
     */
    public final function geTotalOrderDispatchedSum() : int|float
    {
        return $this->salesRepresentative->orders()
            ->whereIn('status_id', [status('Paid'), status('Dispatched'), status('Complete')])
            ->whereBetween('order_date',[$this->from,$this->to])
            ->sum('total');
    }

    /**
     * @return int
     */
    public final function getTotalNumberOfCustomers() : int
    {
        return $this->wholesUsers->where('sales_representative_id', $this->salesRepresentative->id)->count();
    }

    /**
     * @return Collection
     */
    public final function getCustomers() : Collection
    {
        return $this->salesRepresentative->wholesales_users()->with('user')->orderBy('created_at', 'desc')->get();
    }

    /**
     * @return Collection
     */
    public final function getCustomerOrders() : Collection
    {
        return $this->salesRepresentative->orders()->orderBy('created_at', 'desc')->limit(20)->get();
    }


    /**
     * @return array
     */
    public final function getProfileInformation() : array
    {
        return [
            'name' => $this->salesRepresentative->user->name ?? "N/A",
            'email' => $this->salesRepresentative->user->email ?? "N/A",
            'phone' => $this->salesRepresentative->user->phone ?? "N/A",

        ];
    }
}
