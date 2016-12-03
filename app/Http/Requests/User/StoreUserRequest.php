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
            "user.email" => "required|email|unique:users,email",
            "user.first_name" => "required",
            "user.last_name" => "required",
            "user.display_name" => "required",
            "user.birthdate" => "required|date_format:Y-m-d",
            "user.gender" => "required|in:MALE,FEMALE",
            "user.religion" => "required",
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
