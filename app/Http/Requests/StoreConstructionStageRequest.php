<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConstructionStageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id' => ['required', 'exists:projects,id'],
            'name' => ['required', 'string', 'max:255'],
            'progress_percent' => ['required', 'integer', 'min:0', 'max:100'],
            'planned_date' => ['nullable', 'date'],
            'actual_date' => ['nullable', 'date'],
        ];
    }
}
