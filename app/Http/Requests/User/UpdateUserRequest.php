<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            "user.email" => "email|unique:users,email",
            "user.birthdate" => "date_format:Y-m-d",
            "user.gender" => "in:MALE,FEMALE",
            "user.phone" => "size:10",
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
            "user.email.email" => "invalid_email",
            "user.email.unique" => "email_already_exist",
            "user.birthdate.date_format" => "invalid_birthdate",
            "user.gender.in" => "invalid_gender",
            "user.phone.size" => "invalid_phone",
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
