<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskDefinitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:daily,weekly,monthly,on_demand'],
            'is_required' => ['required', 'boolean'],
            'performance_weight' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'site_ids' => ['nullable', 'array'],
            'site_ids.*' => ['exists:sites,id'],
            'consultant_ids' => ['nullable', 'array'],
            'consultant_ids.*' => ['exists:consultants,id'],
            'components' => ['required', 'array', 'min:1'],
            'components.*.label' => ['required', 'string', 'max:255'],
            'components.*.component_type' => ['required', 'string'],
            'components.*.is_required' => ['required', 'boolean'],
            'components.*.options' => ['nullable', 'array'],
        ];
    }
}
