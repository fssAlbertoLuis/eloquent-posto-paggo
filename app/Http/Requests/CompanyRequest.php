<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CompanyRequest extends FormRequest
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
                return $this->companyEditValidation();
            default:
                return $this->companyAddValidation();
        }
    }

    private function companyAddValidation()
    {
        $validation = [
            'name' => 'required|string',
            'cnpj' => 'nullable|numeric|digits:14',
            'user.name' => 'required|string',
            'user.email' => 'required|email|unique:users,email',
            'user.password' => 'required|string|min:8|max:32',
        ];
        return $validation;
    }

    private function companyEditValidation()
    {
        $validation = [
            'name' => 'string',
            'cnpj' => 'nullable|numeric|digits:14',
            'user.name' => 'string',
            'user.email' => 'email',
            'user.password' => 'string|min:8|max:32',
        ];
        return $validation;
    }
}
