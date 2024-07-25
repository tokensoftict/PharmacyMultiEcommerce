<?php
namespace App\Classes;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Statusesclass
{
    public static array $allStatuses = [
        [
            'name'=>'Active',
            'label'=>'primary'
        ],
        [
            'name'=>'Approved',
            'label'=>'success'
        ],
        [
            'name'=>'Paid',
            'label'=>'success'
        ],
        [
            'name'=>'Draft',
            'label'=>'primary'
        ],
        [
            'name'=>'Dispatched',
            'label'=>'success'
        ],
        [
            'name'=>'Pending',
            'label'=>'warning'
        ],
        [
            'name'=>'Complete',
            'label'=>'success'
        ],
        [
            'name'=>'Approved',
            'label'=>'primary'
        ],
        [
            'name'=>'Declined',
            'label'=>'danger'
        ],
        [
            'name'=>'Deleted',
            'label'=>'danger'
        ],
        [
            'name'=>'Cancelled',
            'label'=>'danger'
        ],

        [
            'name'=>'Ready',
            'label'=>'success'
        ],
        [
            'name'=>'Transferred',
            'label'=>'primary'
        ],
        [
            'name'=>'In-Progress',
            'label'=>'warning'
        ],
    ];


    public static function loadSystemStatus()
    {
        foreach (self::$allStatuses as $status)
        {
            DB::table('statuses')->updateOrInsert(['name'=> $status['name']], $status);
        }
    }
}
