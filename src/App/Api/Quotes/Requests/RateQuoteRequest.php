<?php

namespace App\Api\Quotes\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RateQuoteRequest extends FormRequest
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
        // The user can rate from 0 to 5
        // 0 means no rating
        return [
            'score' => [
                'required',
                'integer',
            ],
        ];
    }
}
