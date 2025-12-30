<?php

namespace App\Modules\Post\Payload\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
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
}
