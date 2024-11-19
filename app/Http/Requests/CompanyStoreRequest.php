<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->user()->role !== 'admin') return false;

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
            'name' => ['required', 'string', 'max:255', 'unique:companies'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:companies'],
            'phone' => ['required', 'string', 'max:255', 'unique:companies'],
            'manager' => ['required', 'array'],
            'manager.user_id' => ['required', 'exists:users,id'],
        ];
    }
}
