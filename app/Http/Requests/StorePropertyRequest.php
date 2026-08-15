<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],
            'type' => ['required', 'string', 'in:apartment,office,land'],
            'area' => ['required', 'numeric', 'min:0'],
            'floor' => ['nullable', 'integer', 'min:0'],
            'rooms_count' => ['nullable', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'in:available,reserved,sold'],
        ];
    }
}
