<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class CreateWholeSalesUserRequest extends FormRequest
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
            'business_name' => 'required|unique:wholesales_users,business_name',
            'cac_document'  => 'sometimes|mimes:jpg,jpeg,png,pdf',
            'premises_licence' => 'sometimes|mimes:jpg,jpeg,png,pdf',
            'phone' => 'required',
            'business_email_address' => 'required',
            'address_1' => 'required',
            'state_id' => 'required',
            'town_id' => 'required',
        ];
    }
}
