<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StaffRankingExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $staffs;

    public function __construct($staffs)
    {
        $this->staffs = $staffs;
    }

    public function collection()
    {
        return collect($this->staffs);
    }

    public function headings(): array
    {
        return [
            'Staff Member',
            'Department',
            'Total Feedback',
            'Positive Count',
            'Negative Count',
            'Average Rating'
        ];
    }

    public function map($staff): array
    {
        return [
            $staff->name,
            $staff->department,
            $staff->feedbacks_count,
            $staff->positive_count,
            $staff->negative_count,
            number_format($staff->avg_rating ?? 0, 1)
        ];
    }
}
