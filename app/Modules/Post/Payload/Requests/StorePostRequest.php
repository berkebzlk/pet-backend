<?php

namespace App\Modules\Post\Payload\Requests;

use App\Modules\Core\Payload\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'image', 'max:10240'], // 10MB
            'description' => ['nullable', 'string', 'max:1000'],
            'pet_id' => ['required_without:veterinary_profile_id', 'nullable', 'exists:pets,id'],
            'veterinary_profile_id' => ['required_without:pet_id', 'nullable', 'exists:veterinary_profiles,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'image' => __('post::post.image'),
            'description' => __('post::post.description'),
            'petId' => __('post::post.pet_id'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge($this->snakeCaseArray($this->all()));
    }
}
