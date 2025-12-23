<?php

namespace App\Modules\Match\Payload\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMatchRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'initiator_pet_id' => 'required|exists:pets,id',
            'target_pet_id' => 'required|exists:pets,id|different:initiator_pet_id',
        ];
    }
}
