<?php

namespace App\Modules\Pet\Payload\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:30', 'unique:pets,username', 'regex:/^[a-zA-Z0-9_.]+$/'],
            'type' => ['required', 'string', 'max:50'],
            'breed' => ['nullable', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female'],
            'birthDate' => ['required', 'date', 'before:today'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'isNeutered' => ['boolean'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'max:5120'], // 5MB max
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('pet::pet.name'),
            'type' => __('pet::pet.type'),
            'breed' => __('pet::pet.breed'),
            'gender' => __('pet::pet.gender'),
            'birthDate' => __('pet::pet.birthDate'),
            'weight' => __('pet::pet.weight'),
            'isNeutered' => __('pet::pet.isNeutered'),
            'bio' => __('pet::pet.bio'),
            'image' => __('pet::pet.image'),
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.required'),
            'name.string' => __('validation.string'),
            'name.max' => __('validation.max.string', ['max' => 255]),
            'type.required' => __('validation.required'),
            'type.string' => __('validation.string'),
            'type.max' => __('validation.max.string', ['max' => 50]),
            'breed.string' => __('validation.string'),
            'breed.max' => __('validation.max.string', ['max' => 100]),
            'gender.required' => __('validation.required'),
            'gender.in' => __('validation.in'),
            'birthDate.required' => __('validation.required'),
            'birthDate.date' => __('validation.date'),
            'birthDate.before' => __('validation.before', ['date' => 'today']),
            'weight.numeric' => __('validation.numeric'),
            'weight.min' => __('validation.min.numeric', ['min' => 0]),
            'isNeutered.boolean' => __('validation.boolean'),
            'bio.string' => __('validation.string'),
            'bio.max' => __('validation.max.string', ['max' => 1000]),
            'image.image' => __('validation.image'),
            'image.max' => __('validation.max.file', ['max' => 5120]),
        ];
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        if (array_key_exists('birthDate', $validated)) {
            $validated['birthdate'] = $validated['birthDate'];
            unset($validated['birthDate']);
        }

        if (array_key_exists('isNeutered', $validated)) {
            $validated['is_neutered'] = $validated['isNeutered'];
            unset($validated['isNeutered']);
        }

        return $validated;
    }
}
