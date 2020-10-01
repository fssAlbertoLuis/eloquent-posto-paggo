<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;

class UserRequest extends FormRequest
{
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
        switch ($this->method()) {
            case 'PATCH':
                return $this->userEditValidation();
            default:
                return $this->userAddValidation();
        }
    }

    private function userEditValidation()
    {
        $validation = [
            'name' => 'string',
            'email' => 'email|unique:users,email,'.$this->route('id'),
            'password' => 'string|min:8|max:32',
        ];
        if (Auth::user()->permission === 'admin') {
            $validation['is_admin'] = 'boolean';
        } else {
            if (Auth::user()->id != $this->route('id')) {
                $validation['permission'] = 'string|in:manager,user';
            }
        }
        return $validation;
    }

    private function userAddValidation()
    {
        $validation = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|max:32',
        ];
        if (Auth::user()->permission === 'admin') {
            $validation['is_admin'] = 'boolean';
        } else {
            $validation['permission'] = 'required|string|in:manager,user';
        }
        return $validation;
    }
}
