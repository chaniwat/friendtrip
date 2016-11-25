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
            "event.name" => "required",
            "event.destination" => "required",
            "event.appointment_place" => "required",
            "event.start_date" => "required|date_format:Y-m-d H:i:s",
            "event.end_date" => "required|date_format:Y-m-d H:i:s|after:event.start_date",
            "event.approximate_cost" => "required",
            "event.details" => "required",
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
            "event.name.required" => "event_no_name_given",
            "event.destination.required" => "event_no_destination_given",
            "event.appointment_place.required" => "event_no_appoint_place_given",
            "event.start_date.required" => "event_no_start_date_given",
            "event.end_date.required" => "event_no_end_date_given",
            "event.approximate_cost.required" => "event_no_approx_cost_given",
            "event.details.required" => "event_no_details_given",
            "event.start_date.date_format" => "event_start_date_invalid_format",
            "event.end_date.date_format" => "event_end_date_invalid_format",
            "event.end_date.after" => "event_end_date_must_after_start_date",
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
        return ["error" => $validator->errors()->all()];
    }
}
