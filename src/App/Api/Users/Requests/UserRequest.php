<?php

namespace App\Api\Users\Requests;

use Domain\Users\Enums\SexEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:80',
            'sex' => ['nullable', new Enum(SexEnum::class)],
            'birthday' => ['bail', 'nullable', 'date', 'before:today', 'after:1900-01-01'],
            'email' => 'required|email|unique:users',
            'password' => [
                'required',
                Password::min(8) // require at least 8 characters
                    ->letters() // require at least one letter
                    ->mixedCase() // require both upper and lower case letters
                    ->numbers() // require at least one number
                    ->symbols() // require at least one symbol
                    ->uncompromised(), // do not allow compromised passwords
            ],
        ];
    }
}
