<?php

namespace App\Http\Requests\GroupRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GroupCreateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'admin_id' => 'required',
            'name' => 'required|max:22|min:8|string',
            'description' => 'required|max:22|min:8|string'
        ];
    }



    public function messages()
    {
        return [
            'admin_id.required' => 'l\'id de l\'admin est requis',
            'name.required' => 'le nom est requis',
            'name.min' => 'le nom dois etre entre 8 et 22 caracteres',
            'name.max' => 'le nom dois etre entre 8 et 22 caracteres',
            'description.required' => 'la description est requis',
            'description.min' => 'la description dois etre entre 8 et 22 caracteres',
            'description.max' => 'la description  dois etre entre 8 et 22 caracteres',

        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Echec de validation.',
            'data' => $validator->errors()
        ]));
    }
}
