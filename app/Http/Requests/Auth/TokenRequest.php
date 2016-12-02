<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class TokenRequest extends FormRequest
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
        return [
            "email" => "required",
            "password" => "required"
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            "email.required" => "no_email_given",
            "password.required" => "no_password_given",
        ];
    }

    /**
     * Custom errors format
     *
     * @param Validator $validator
     * @return array
     */
    protected function formatErrors(Validator $validator)
    {
        return ["message" => $validator->errors()->all()];
    }

}
