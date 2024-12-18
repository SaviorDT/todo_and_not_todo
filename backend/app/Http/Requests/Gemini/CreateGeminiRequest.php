<?php

namespace App\Http\Requests\Gemini;

use Illuminate\Foundation\Http\FormRequest;

class CreateGeminiRequest extends FormRequest
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
            'userinput' => 'required|string|max:255',
            'api_key' => 'required|string|max:1024',
        ];
    }
}
