<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentUpdateRequest extends FormRequest
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
        $studentId = auth('student')->id();

        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', Rule::unique('students', 'email')->ignore($studentId)],
            'email_verified_at',
            'date_of_birth' => ['required', 'date'],
            'gender' => [ Rule::in(['male', 'female']), 'string'],
            'id_card_number' => ['required', 'string', 'size:14', Rule::unique('students', 'id_card_number')->ignore($studentId)],
            'phone_number' => ['required', 'string', 'size:11', 'regex:/^01[0125]\d{8}$/', Rule::unique('students', 'phone_number')->ignore($studentId)],
            'address' => ['required', 'string', 'max:277'],
            'avater' => ['nullable', 'image', 'mimes:jpg,png'],
            'city' => ['required', 'string', 'max:277'],
            'school' => ['required', 'string', 'max:277'],
            'grade' => ['required', 'string' , 'max:277'],
        ];
    }
}
