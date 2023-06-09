<?php

namespace App\Http\Requests\Apartment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApartmentRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'required|max:255',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'address' => 'required|max:512',
            'visibility' => 'required|boolean',
            'price' => 'required|numeric|min:60|max:1500',
            'rooms_number' => 'required|numeric|min:1|max:8',
            'bathrooms_number' => 'required|numeric|min:1|max:8',
            'beds_number' => 'required|numeric|min:1|max:16',
            'description' => 'required|min:10|max:4096',
            'size' => 'required|numeric|min:50|max:500',
            'user_id' => 'required|numeric'
        ];
    }
}