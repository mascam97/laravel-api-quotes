<?php

use Illuminate\Support\Arr;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNotSame;
use function PHPUnit\Framework\assertTrue;

it('has same keys', function (string $file) {
    $enTranslations = require base_path("lang/en/$file.php");
    $esTranslations = require base_path("lang/es/$file.php");

    $enKeys = array_keys(Arr::dot($enTranslations));
    $esKeys = array_keys(Arr::dot($esTranslations));

    expect($enKeys)->toEqual($esKeys);
})->with([
    'auth',
    'mail',
    'message',
    'pagination',
    'passwords',
    'validation',
]);

it('validates not empty values', function (string $file) {
    $enTranslations = require base_path("lang/en/$file.php");
    $esTranslations = require base_path("lang/es/$file.php");

    $enKeys = array_keys(Arr::dot($enTranslations));
    $esKeys = array_keys(Arr::dot($esTranslations));

    foreach ($enKeys as $key) {
        $value = Arr::get($enTranslations, $key);

        assertNotNull($value, "Empty value found for key: $key");
        assertFalse(is_numeric($value), "Value is numeric for key: $key");
        assertTrue(is_string($value), "Value is not a string for key: $key");
    }

    foreach ($esKeys as $key) {
        $value = Arr::get($esTranslations, $key);
        assertNotNull($value, "Empty value found for key: $key");
    }
})->with([
    'auth',
    'mail',
    'message',
    'pagination',
    'passwords',
    'validation',
]);

it('validates both data are not the same', function (string $file) {
    $enTranslations = require base_path("lang/en/$file.php");
    $esTranslations = require base_path("lang/es/$file.php");

    $enKeys = array_keys(Arr::dot($enTranslations));

    foreach ($enKeys as $key) {
        $enValue = Arr::get($enTranslations, $key);
        $esValue = Arr::get($esTranslations, $key);

        assertNotSame($enValue, $esValue, "Same value found for key: $key");
    }
})->with([
    'auth',
    'mail',
    'message',
    'pagination',
    'passwords',
    'validation',
]);
