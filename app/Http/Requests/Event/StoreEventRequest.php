<?php

namespace App\Http\Requests\Event;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
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
            "name" => "required",
            "destination_place" => "required",
            "destination_place_id" => "integer",
            "destination_latitude" => "numeric",
            "destination_longitude" => "numeric",
            "start_date" => "required|date_format:Y-m-d H:i:s",
            "end_date" => "required|date_format:Y-m-d H:i:s|after:start_date",
            "appointment_place" => "required",
            "appointment_place_id" => "integer",
            "appointment_latitude" => "numeric",
            "appointment_longitude" => "numeric",
            "appointment_time" => "required|date_format:Y-m-d H:i:s|before:start_date",
            "details" => "required",
            "type" => "required",
            "approximate_cost" => "required|numeric",
            "settings" => "array"
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
