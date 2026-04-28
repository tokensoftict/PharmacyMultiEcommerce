<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CouponUsageExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $usages;

    public function __construct($usages)
    {
        $this->usages = $usages;
    }

    public function collection()
    {
        return collect($this->usages);
    }

    public function headings(): array
    {
        return [
            'Coupon Code',
            'Customer Name',
            'Customer Phone',
            'Environment',
            'Use Date'
        ];
    }

    public function map($usage): array
    {
        $customerName = 'Unknown';
        $customerPhone = 'Unknown';

        if ($usage->customer && $usage->customer->user) {
            $customerName = $usage->customer->user->name;
            $customerPhone = $usage->customer->user->phone ?? ($usage->customer->phone ?? 'Unknown');
        }

        return [
            $usage->code,
            $customerName,
            $customerPhone,
            $usage->app ? $usage->app->name : 'Unknown',
            $usage->use_date ? $usage->use_date->format('Y-m-d H:i:s') : 'Unknown'
        ];
    }
}
