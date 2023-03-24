<?php

use Domain\Rating\Models\Rating;

return [
    'models' => [
        'rating' => Rating::class,
    ],
    'min' => 0,
    'max' => 5,
];
