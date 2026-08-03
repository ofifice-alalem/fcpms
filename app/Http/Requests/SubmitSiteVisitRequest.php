<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitSiteVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('fill_daily_tasks');
    }

    public function rules(): array
    {
        return [
            'site_visit_id' => ['required', 'exists:site_visits,id'],
            'responses' => ['required', 'array'],
            'responses.*.task_definition_id' => ['required', 'exists:task_definitions,id'],
            'responses.*.is_completed' => ['required', 'boolean'],
            'responses.*.answers' => ['nullable', 'array'],
        ];
    }
}
