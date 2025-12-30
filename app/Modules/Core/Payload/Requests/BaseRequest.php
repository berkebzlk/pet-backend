<?php

namespace App\Modules\Core\Payload\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

abstract class BaseRequest extends FormRequest
{
    protected function snakeCaseArray(array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $snakeKey = Str::snake($key);

            $result[$snakeKey] = is_array($value)
                ? $this->snakeCaseArray($value)
                : $value;
        }

        return $result;
    }
}