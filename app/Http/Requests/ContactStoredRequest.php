<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactStoredRequest extends FormRequest
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
            'display_name' => 'required|string',
            'name' => 'required|string',
            'phone1' => 'required|integer',
            'phone2' => 'nullable|integer',
            'phone3' => 'nullable|integer',
            'email1' => 'nullable|email',
            'email2' => 'nullable|email',
            'email3' => 'nullable|email',
            'address1' => 'nullable|string',
            'address2' => 'nullable|string',
        ];
    }
}
