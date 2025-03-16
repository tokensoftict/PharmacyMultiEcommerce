<?php

namespace App\Livewire\Backend\Admin\SalesRep;

use App\Models\SalesRepresentative;
use Livewire\Component;

class ShowDashboard extends Component
{

    public SalesRepresentative $salesRepresentative;
    public string $from, $to, $month, $totalDispatchedCount, $totalDispatchedSum;

    public function mount()
    {
        $this->from = date('Y-m-01');
        $this->to = date('Y-m-t');

        if(isset(request()->from) && isset(request()->to)){
            $this->from = request()->from;
            $this->to = request()->to;
        }

        if(date("m",strtotime($this->from)) == date("m",strtotime($this->to))){
            $this->month = date('F');
        }else{
            $this->month = date("F d Y",strtotime($this->from))." - ".date("F d Y",strtotime($this->to));
        }

        $this->totalDispatchedCount = $this->salesRepresentative->orders()->whereIn('status_id', [status('Paid'), status('Dispatched'), status('Complete')])->whereBetween('order_date',[$this->from,$this->to])->count();
        $this->totalDispatchedSum =  $this->salesRepresentative->orders()->whereIn('status_id', [status('Paid'), status('Dispatched'), status('Complete')])->whereBetween('order_date',[$this->from,$this->to])->sum('total');
    }


    public function render()
    {
        return view('livewire.backend.admin.sales-rep.show-dashboard');
    }

}
