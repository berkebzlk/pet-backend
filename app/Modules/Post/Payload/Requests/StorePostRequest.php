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
            'pet_id' => ['required', 'exists:pets,id'],
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
