<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConsultantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage_users');
    }

    public function rules(): array
    {
        $consultantId = $this->route('consultant');

        return [
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'work_schedule_template_id' => ['nullable', 'exists:work_schedule_templates,id'],
            'status' => ['required', 'in:active,inactive,vacation'],
        ];
    }
}
