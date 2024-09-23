<?php

namespace App\Http\Requests\AuthRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AdminRequest extends FormRequest
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
            'user_id' => 'required',
            'fullname' => 'required|max:50|min:5|string'
        ];
    }



    public function messages()
    {
        return [
            'user_id.required' => 'Id is requis',
            'fullname.max' => 'Email doit etre entre 5 et 50 caracteres',
            'email.min' => 'Email doit etre entre 5 et 50 caracteres',
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
