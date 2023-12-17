<?php

namespace Domain\Rating\Exceptions;

use Illuminate\Http\JsonResponse;
use Support\DomainException;

class InvalidScoreException extends DomainException
{
    public function __construct(
        private readonly int $min,
        private readonly int $max
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
