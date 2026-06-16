<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        // $employeeId = $this->route('employee');
        $employeeId = $this->route('employeeId');;

        return [

            'name' => ['sometimes','string','max:255'],

            'email' => ['sometimes','email',Rule::unique('users','email')->ignore($employeeId)],

            'phone' => ['nullable','string','max:20'],

            'position' => ['nullable','string','max:255'],

            'department_id' => ['sometimes','exists:departments,id'],

            'base_salary' => ['sometimes','numeric','min:0'],'hire_date' => ['nullable','date'],

            'status' => ['sometimes','in:active,inactive,suspended'],

            'address' => ['nullable','string'],

            'national_id' => ['nullable',Rule::unique('users','national_id')->ignore($employeeId)],

            'profile_image' => ['nullable','image','mimes:jpg,jpeg,png','max:2048']

        ];

    }
}
