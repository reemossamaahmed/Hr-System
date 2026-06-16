<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
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
        return [
            'name' => ['required','string','min:2','max:255'],
            'email' => ['required','email','unique:users,email'],
            // 'password' => ['required','min:6','confirmed'],
            'phone' => ['nullable','string','max:20'],
            'position' => ['nullable','string','max:255'],
            'department_id' => ['required','numeric','exists:departments,id'],
            'base_salary' => ['required','numeric','min:0'],
            'hire_date' => ['nullable','date'],
            'address' => ['nullable','string'],
            'national_id' => ['nullable','string','unique:users,national_id'],
            'profile_image' => ['nullable','image','mimes:jpg,jpeg,png','max:2048'],
        ];
    }
}






















