<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRatingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'movie_title' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'rating' => 'required|integer', // Adjust the min and max values as needed
            'r_description' => 'required|string|max:1000',
        ];
    }
}
