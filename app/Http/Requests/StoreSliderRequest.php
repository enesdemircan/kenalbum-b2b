<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSliderRequest extends FormRequest
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'link' => 'nullable|url|max:255',
            'order' => 'nullable|integer|min:1',
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
            'image.required' => 'Slider resmi zorunludur.',
            'image.image' => 'Slider resmi geçerli bir resim formatında olmalıdır.',
            'image.mimes' => 'Slider resmi jpeg, png, jpg veya gif formatında olmalıdır.',
            'image.max' => 'Slider resmi 2MB\'dan büyük olamaz.',
            'title.max' => 'Başlık 255 karakterden uzun olamaz.',
            'description.max' => 'Açıklama 500 karakterden uzun olamaz.',
            'link.url' => 'Link geçerli bir URL olmalıdır.',
            'link.max' => 'Link 255 karakterden uzun olamaz.',
            'order.integer' => 'Sıra numarası tam sayı olmalıdır.',
            'order.min' => 'Sıra numarası 1\'den küçük olamaz.',
        ];
    }
} 