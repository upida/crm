<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if (!JWTAuth::getToken() || ! in_array($this->user()->role, ['admin', 'manager'])) {
            return false;
        }

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
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,'.$this->user()->id,
            'phone' => 'sometimes|unique:users,'.$this->user()->id,
            'address' => 'sometimes|string|max:255',
            'role' => 'sometimes|in:admin,manager,employee',
            'password' => 'sometimes|string|min:6|confirmed',
        ];
    }
}
