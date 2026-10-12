<?php

declare(strict_types=1);

namespace App\Api\Profile\Requests;

use Domain\Users\Enums\SexEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:80'],
            'sex' => ['sometimes', 'nullable', new Enum(SexEnum::class)],
            'birthday' => ['sometimes', 'nullable', 'date', 'before:today', 'after:1900-01-01'],
        ];
    }
}
