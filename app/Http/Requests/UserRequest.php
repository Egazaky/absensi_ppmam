<?php

namespace App\Http\Requests;

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
        $userRole = auth()->user()->role;

        // SuperAdmin dan Administrator bisa assign SuperAdmin
        // Role lain hanya bisa assign Administrator, Pengurus, Santri
        $allowedRoles = ($userRole == 'SuperAdmin' || $userRole == 'Administrator')
            ? 'SuperAdmin,Administrator,Pengurus,Santri'
            : 'Administrator,Pengurus,Santri';

        return [
            'santri_id' => 'required|exists:santris,id|unique:users,santri_id,'.$this->user,
            'email' => 'required|string|email|max:255|unique:users,email,'.$this->user,
            'password' => 'required|string|confirmed|min:8',
            'role'  => 'required|in:' . $allowedRoles
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }
}
