<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBankAccountRequest extends FormRequest
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
            'bank_name' => 'required|string|max:255',
            'iban' => 'required|string|max:50',
            'account_name' => 'required|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'bank_name.required' => 'Banka adı zorunludur.',
            'bank_name.max' => 'Banka adı 255 karakterden uzun olamaz.',
            'iban.required' => 'IBAN numarası zorunludur.',
            'iban.max' => 'IBAN numarası 50 karakterden uzun olamaz.',
            'account_name.required' => 'Hesap sahibi adı zorunludur.',
            'account_name.max' => 'Hesap sahibi adı 255 karakterden uzun olamaz.',
        ];
    }
} 