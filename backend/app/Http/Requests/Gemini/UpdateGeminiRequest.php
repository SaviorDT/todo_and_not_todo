<?php

namespace App\Http\Requests\Gemini;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGeminiRequest extends FormRequest
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
            'title' => 'string|max:255',
            'description' => 'string|max:10240',
            'start_date' => 'date',
            'due_date' => 'date',
            'completed' => 'boolean',
            'id' => 'required|exists:todos'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }
}