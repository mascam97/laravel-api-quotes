<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class InvalidScore extends Exception
{
    public function __construct(
        private int $min,
        private int $max
    ) {
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => 'The given data was invalid.',
            'errors' => [
                'score' => [
                    trans('validation.between.numeric', [
                        'attribute' => 'score',
                        'min' => $this->min,
                        'max' => $this->max,
                    ]),
                ],
            ],
        ]);
    }
}
