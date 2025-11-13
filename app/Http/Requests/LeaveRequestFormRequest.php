<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeaveRequestFormRequest extends FormRequest
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
            'leave_type' => [
                'required',
                'string',
                'in:paid_time_off,unpaid_leave,sick_leave,vacation',
            ],
            'start_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
            ],
            'employee_notes' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'leave_type' => 'leave type',
            'start_date' => 'start date',
            'end_date' => 'end date',
            'employee_notes' => 'notes',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'leave_type.in' => 'Please select a valid leave type.',
            'start_date.after_or_equal' => 'Start date cannot be in the past.',
            'end_date.after_or_equal' => 'End date must be on or after the start date.',
        ];
    }
}
