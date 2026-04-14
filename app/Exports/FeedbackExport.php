<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class FeedbackExport implements FromCollection, WithHeadings, WithMapping
{
    protected $feedbacks;

    public function __construct(Collection $feedbacks)
    {
        $this->feedbacks = $feedbacks;
    }

    public function collection(): Collection
    {
        return $this->feedbacks;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Full Name',
            'Phone Number',
            'Store',
            'Department',
            'Invoice Number',
            'Rating',
            'Staff',
            'Type',
            'Feedback',
            'Submitted At',
        ];
    }

    public function map($feedback): array
    {
        return [
            $feedback->id,
            $feedback->full_name,
            $feedback->phone_number,
            $feedback->store,
            $feedback->department,
            $feedback->invoice_number,
            $feedback->rating,
            $feedback->staff?->name ?? 'N/A',
            $feedback->feedback_type,
            $feedback->feedback,
            $feedback->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
