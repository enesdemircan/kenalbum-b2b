<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSiteSettingRequest extends FormRequest
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
            'logo' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'facebook' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'company_title' => 'nullable|string|max:255',
            'announcement' => 'nullable|string|max:1000',
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
            'logo.file' => 'Logo dosyası geçerli bir dosya formatında olmalıdır.',
            'logo.mimes' => 'Logo dosyası jpeg, png, jpg, gif veya svg formatında olmalıdır.',
            'logo.max' => 'Logo dosyası 2MB\'dan büyük olamaz.',
            'title.required' => 'Site başlığı zorunludur.',
            'title.max' => 'Site başlığı 255 karakterden uzun olamaz.',
            'description.max' => 'Açıklama 500 karakterden uzun olamaz.',
            'phone.max' => 'Telefon numarası 20 karakterden uzun olamaz.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'email.max' => 'E-posta adresi 255 karakterden uzun olamaz.',
            'address.max' => 'Adres 500 karakterden uzun olamaz.',
            'facebook.url' => 'Facebook linki geçerli bir URL olmalıdır.',
            'twitter.url' => 'Twitter linki geçerli bir URL olmalıdır.',
            'instagram.url' => 'Instagram linki geçerli bir URL olmalıdır.',
            'youtube.url' => 'YouTube linki geçerli bir URL olmalıdır.',
            'tax_rate.numeric' => 'Vergi oranı sayısal bir değer olmalıdır.',
            'tax_rate.min' => 'Vergi oranı 0\'dan küçük olamaz.',
            'tax_rate.max' => 'Vergi oranı 100\'den büyük olamaz.',
            'company_title.max' => 'Şirket başlığı 255 karakterden uzun olamaz.',
            'announcement.max' => 'Duyuru 1000 karakterden uzun olamaz.',
        ];
    }
} 