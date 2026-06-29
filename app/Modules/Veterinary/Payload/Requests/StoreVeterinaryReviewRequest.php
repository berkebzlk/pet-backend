<?php

namespace App\Modules\Veterinary\Payload\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVeterinaryReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Must own the pet that is making the review
        $petId = $this->input('pet_id');
        return $this->user()->pets()->where('id', $petId)->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'pet_id' => ['required', 'integer', 'exists:pets,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
