<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            "user.name" => "required",
            "user.email" => "required|unique:users,email",
            "user.gender" => "required|in:MALE,FEMALE",
            "user.phone" => "size:10",
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
            "user.name.required" => "user_no_name_given",
            "user.email.required" => "user_no_email_given",
            "user.gender.required" => "user_no_sex_given",
            "password.required" => "user_no_password_given",
            "user.email.unique" => "user_email_already_used",
            "user.gender.in" => "user_sex_value_not_accept",
            "user.phone.size" => "user_invalid_phone_length"
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
