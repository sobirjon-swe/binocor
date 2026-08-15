<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'property_id' => [
                'required',
                'exists:properties,id',
                Rule::unique('contracts', 'property_id')
                    ->where('status', 'active')
                    ->ignore($this->route('contract')),
            ],
            'total_price' => ['required', 'numeric', 'min:0'],
            'payment_type' => ['required', 'string', 'in:cash,installment'],
            'signed_date' => ['required', 'date'],
            'status' => ['required', 'string', 'in:active,cancelled,completed'],
        ];
    }

    public function messages(): array
    {
        return [
            'property_id.unique' => 'Bu obyekt uchun allaqachon faol shartnoma mavjud.',
        ];
    }
}
