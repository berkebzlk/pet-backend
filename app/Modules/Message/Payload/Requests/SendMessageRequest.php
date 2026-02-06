<?php

namespace App\Modules\Message\Payload\Requests;

use App\Modules\Core\Payload\Requests\BaseRequest;

class SendMessageRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'receiver_pet_id' => ['required', 'exists:pets,id'],
            'content' => ['required', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'receiver_pet_id' => __('message.receiver_pet_id'),
            'content' => __('message.content'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge($this->snakeCaseArray($this->all()));
    }
}
