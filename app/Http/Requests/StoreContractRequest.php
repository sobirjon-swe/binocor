<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContractRequest extends FormRequest
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
                Rule::unique('contracts', 'property_id')->where('status', 'active'),
            ],
            'total_price' => ['required', 'numeric', 'min:0'],
            'payment_type' => ['required', 'string', 'in:cash,installment'],
            'down_payment' => ['nullable', 'numeric', 'min:0', 'lt:total_price'],
            'installment_months' => ['nullable', 'required_if:payment_type,installment', 'integer', 'min:1', 'max:60'],
            'signed_date' => ['required', 'date'],
            'status' => ['required', 'string', 'in:active,cancelled,completed'],
        ];
    }

    public function messages(): array
    {
        return [
            'property_id.unique' => 'Bu obyekt uchun allaqachon faol shartnoma mavjud.',
            'down_payment.lt' => 'Boshlang\'ich to\'lov umumiy narxdan kichik bo\'lishi kerak.',
            'installment_months.required_if' => 'Rassrochka uchun oylar sonini kiriting.',
        ];
    }
}
