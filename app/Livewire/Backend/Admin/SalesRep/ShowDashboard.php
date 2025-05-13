<?php

namespace App\Livewire\Backend\Admin\SalesRep;

use App\Classes\ApplicationEnvironment;
use App\Mail\SalesRep\SalesRepInvitationMail;
use App\Models\SalesRepresentative;
use App\Services\SalesRepresentative\ReportService;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class ShowDashboard extends Component
{

    public SalesRepresentative $salesRepresentative;
    public string $from, $to, $month, $totalDispatchedCount, $totalDispatchedSum;
    private ReportService $reportService;



    public function mount()
    {

        $this->reportService = app(ReportService::class);
        $this->reportService->setSalesRepresentative($this->salesRepresentative);

        $this->month =  $this->reportService->month;
        $this->from = $this->reportService->from;
        $this->to = $this->reportService->to;

        $this->totalDispatchedCount = $this->reportService->geTotalOrderDispatchedCount();
        $this->totalDispatchedSum =  $this->reportService->geTotalOrderDispatchedSum();
    }


    public function render()
    {
        $this->reportService = app(ReportService::class);
        $this->reportService->setSalesRepresentative($this->salesRepresentative);

        $this->month =  $this->reportService->month;
        $this->from = $this->reportService->from;
        $this->to = $this->reportService->to;

        $this->totalDispatchedCount = $this->reportService->geTotalOrderDispatchedCount();
        $this->totalDispatchedSum =  $this->reportService->geTotalOrderDispatchedSum();

        return view('livewire.backend.admin.sales-rep.show-dashboard');
    }


    /**
     * @return void
     */
    public function resendInvitation()
    {
        $token = sha1(md5(generateRandomString(50)));
        $this->salesRepresentative->token = $token;
        $this->salesRepresentative->save();
        $this->salesRepresentative->fresh();
        $link = route('sales-representative.sales_rep.accept-invitation', $this->salesRepresentative->token);
        Mail::to($this->salesRepresentative->user->email)->send(new SalesRepInvitationMail($this->salesRepresentative, $link));
        $this->alert('success', 'An Invitation Email has been re-sent to ' . $this->salesRepresentative->user->email . " " . $this->salesRepresentative->user->name . " will become a sales representative when they accept the invite  &#128513;");
    }
}
