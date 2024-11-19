<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyIndexRequest extends FormRequest
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
            'search' => ['nullable', 'string'],
            'order_by' => ['nullable', 'in:name,email,phone'],
            'order_direction' => ['nullable', 'in:asc,desc'],
            'limit' => ['nullable', 'integer'],
            'offset' => ['nullable', 'integer'],
        ];
    }
}
