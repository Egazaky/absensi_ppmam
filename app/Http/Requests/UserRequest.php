<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public $validator = null;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $allowedRoles = auth()->user()->isSuperAdmin()
            ? implode(',', [User::ROLE_SUPER_ADMIN, User::ROLE_ADMINISTRATOR, User::ROLE_PENGURUS, User::ROLE_SANTRI])
            : implode(',', [User::ROLE_ADMINISTRATOR, User::ROLE_PENGURUS, User::ROLE_SANTRI]);
        $userId = $this->route('pengguna');

        return [
            'santri_id' => 'required|exists:santris,id|unique:users,santri_id,'.$userId,
            'email' => 'required|string|email|max:255|unique:users,email,'.$userId,
            'password' => 'required|string|confirmed|min:8',
            'role' => 'required|in:'.$allowedRoles,
        ];
    }

    protected function prepareForValidation()
    {
        $userId = $this->route('pengguna');

        if (! $userId) {
            return;
        }

        $user = User::find($userId);

        if (! $user) {
            return;
        }

        $this->merge([
            'santri_id' => $this->input('santri_id', $user->santri_id),
            'role' => $this->input('role', $user->role),
        ]);
    }

    protected function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }
}
