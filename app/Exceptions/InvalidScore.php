<?php

namespace App\Exceptions;

use Exception;

class InvalidScore extends Exception
{
    private $min;

    private $max;

    public function __construct(int $min, int $max)
    {
        $this->min = $min;
        $this->max = $max;
    }

    public function render()
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
