<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestGeoDataForGooglePlacesCall extends FormRequest
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
            'placeid' => ['required','max:55','min:10'],
            'type' => ['required','max:25'],            
            'lat' => ['required','max:20','min:4'],            
            'lng' => ['required','max:20','min:4'],            
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
            'placeid.required' => 'The Google Place ID field is required.',                        
        ];
    }    
}
