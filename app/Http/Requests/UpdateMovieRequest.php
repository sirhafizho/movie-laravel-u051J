<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMovieRequest extends FormRequest
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
            'title' => 'string|max:255',
            'release' => 'date',
            'length' => 'integer',
            'description' => 'string',
            'mpaa_rating' => 'string|max:255',
            'genre_1' => 'string|max:255',
            'genre_2' => 'nullable|string|max:255',
            'genre_3' => 'nullable|string|max:255',
            'director' => 'string|max:255',
            'performer_1' => 'string|max:255',
            'performer_2' => 'nullable|string|max:255',
            'performer_3' => 'nullable|string|max:255',
            'language' => 'string|max:255',
            'poster' => 'nullable|string',
            'overall_rating' => 'nullable|numeric|between:0,10',
            'views' => 'nullable|integer|min:0',
        ];
    }
}
