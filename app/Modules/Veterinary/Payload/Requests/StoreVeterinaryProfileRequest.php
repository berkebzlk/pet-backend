<?php

namespace App\Modules\Veterinary\Payload\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVeterinaryProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clinic_name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'string', 'max:255'],
            'about' => ['nullable', 'string', 'max:2000'],
            'specialties' => ['nullable', 'array'],
            'specialties.*' => ['string', 'max:50'],
            'profile_photo' => ['nullable', 'image', 'max:5120'], // 5MB max
            'cover_photo' => ['nullable', 'image', 'max:5120'], // 5MB max
        ];
    }

    public function attributes(): array
    {
        return [
            'clinic_name' => __('veterinary::veterinary.clinic_name'),
            'city' => __('veterinary::veterinary.city'),
            'phone' => __('veterinary::veterinary.phone'),
            'website' => __('veterinary::veterinary.website'),
            'about' => __('veterinary::veterinary.about'),
            'specialties' => __('veterinary::veterinary.specialties'),
            'profile_photo' => __('veterinary::veterinary.profile_photo'),
            'cover_photo' => __('veterinary::veterinary.cover_photo'),
        ];
    }

    public function messages(): array
    {
        return [
            'clinic_name.required' => __('validation.required'),
            'clinic_name.string' => __('validation.string'),
            'clinic_name.max' => __('validation.max.string', ['max' => 255]),
            'city.required' => __('validation.required'),
            'city.string' => __('validation.string'),
            'city.max' => __('validation.max.string', ['max' => 255]),
            'phone.string' => __('validation.string'),
            'phone.max' => __('validation.max.string', ['max' => 50]),
            'website.string' => __('validation.string'),
            'website.max' => __('validation.max.string', ['max' => 255]),
            'about.string' => __('validation.string'),
            'about.max' => __('validation.max.string', ['max' => 2000]),
            'specialties.array' => __('validation.array'),
            'profile_photo.image' => __('validation.image'),
            'profile_photo.max' => __('validation.max.file', ['max' => 5120]),
            'cover_photo.image' => __('validation.image'),
            'cover_photo.max' => __('validation.max.file', ['max' => 5120]),
        ];
    }
}
