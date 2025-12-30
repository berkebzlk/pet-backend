<?php

namespace App\Modules\Post\Payload\Requests;

use App\Modules\Core\Payload\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:1000'],
            'pet_id' => ['required', 'exists:pets,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'content' => __('post::comment.content'),
            'pet_id' => __('post::post.pet_id'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge($this->snakeCaseArray($this->all()));
    }
}
