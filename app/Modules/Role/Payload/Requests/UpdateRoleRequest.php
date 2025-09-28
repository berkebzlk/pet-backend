<?php

namespace App\Modules\Role\Payload\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255', 'unique:roles,name,' . $this->route('role')],
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => __('validation.string'),
            'name.max' => __('validation.max'),
            'name.unique' => __('validation.unique'),
        ];
    }
}