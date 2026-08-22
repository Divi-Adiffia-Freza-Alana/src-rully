<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('karyawan')?->id;
        $isCreate = $this->isMethod('POST');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => [$isCreate ? 'required' : 'nullable', Password::min(8)],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
