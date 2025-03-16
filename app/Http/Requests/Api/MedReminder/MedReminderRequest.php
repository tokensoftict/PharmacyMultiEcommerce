<?php

namespace App\Http\Requests\Api\MedReminder;

use Illuminate\Foundation\Http\FormRequest;

class MedReminderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'stock_id' => 'required|exists:stocks,id',
            'drug_name' => 'required',
            'dosage' => 'required',
            'total_dosage_in_package' => 'required',
            'type' => 'required',
            'use_interval' => 'required',
            'interval' => 'required_if:use_interval,1',
            'every' => 'required_if:use_interval,1',
            'start_date_time' => 'required',
            'normal_schedules' => 'required_if:use_interval,0',
        ];
    }
}
