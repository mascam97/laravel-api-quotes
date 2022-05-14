<?php

use Domain\Rating\Models\Rating;

return [
    'models' => [
        'rating' => Rating::class,
    ],
    'min' => 1,
    'max' => 5,
];
