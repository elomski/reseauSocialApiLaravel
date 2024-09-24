<?php

namespace App\Http\Requests\GroupRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class FileRequest extends FormRequest
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
           'file' => 'required|file|mimes:png,jpg,pdf,doc,docx,zip',
            'group_id' => 'required|exists:groups,id'
        ];
    }


    public function messages()
    {
        return [
            'file.required' => 'le ficher est requis',
            'file.max' => 'le ficher doit avoir au max 10000000',
            'group_id.required' => 'l\'id est requis',
            'group_id.exists' => 'l\'id excite deja',
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

