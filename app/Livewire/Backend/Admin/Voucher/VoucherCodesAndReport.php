<?php

namespace App\Livewire\Backend\Admin\Voucher;

use App\Classes\ApplicationEnvironment;
use App\Models\Voucher;
use Livewire\Component;

class VoucherCodesAndReport extends Component
{

    public $id;

    public Voucher $voucher;

    public function mount()
    {
        $this->voucher = Voucher::find($this->id);
    }

    public function render()
    {
        return view('livewire.backend.admin.voucher.voucher-codes-and-report');
    }


    /**
     * @return void
     */
    public function approveVoucher()
    {
        $this->voucher->status_id = status('Approved');
        $this->voucher->save();
        $this->alert('success', "Voucher has been approved successfully");

    }


    /**
     * @return void
     */
    public function cancelVoucher()
    {
        $this->voucher->status_id = status('Cancelled');
        $this->voucher->save();
        $this->alert('success', "Voucher has been cancelled successfully");
    }


    /**
     * @return null
     */
    public function deleteVoucher()
    {
        $this->voucher->delete();
        $this->alert('success', "Voucher has been cancelled successfully");
        return $this->redirect(route(ApplicationEnvironment::$storePrefix.'backend.admin.voucher.list'), true);
    }

}
