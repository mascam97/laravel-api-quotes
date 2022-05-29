<?php

namespace App\Api\Users\Requests;

use Domain\Users\Enums\SexEnum;
use Illuminate\Foundation\Http\FormRequest;

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
            'name' => 'required',
            'sex' => 'enum:'.SexEnum::class,
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ];
    }
}
