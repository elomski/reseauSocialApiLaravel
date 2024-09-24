<?php

namespace App\Http\Requests\GroupRequest;

use Illuminate\Foundation\Http\FormRequest;

class MemberCreateRequest extends FormRequest
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
            'group_id' => 'required',
        ];
    }

    public function messages()
    {
        return [

            'user_id' => 'required',
            'group_id' => 'required',
        ];
        }
}
