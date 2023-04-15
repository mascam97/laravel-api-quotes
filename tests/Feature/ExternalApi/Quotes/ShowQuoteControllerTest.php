<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\getJson;
use Services\ExternalApi\ExternalApiService;

beforeEach(function () {
    login();
});

it('can show a quote', function () {
    ExternalApiService::fake(['https://example.com/quotes/10' => Http::response(fixture('ExternalApi/Quote'))]);

    getJson(route('external-api.quotes.show', ['quote' => 10]))
        ->assertOk()
        ->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $data) {
                $data->where('id', 10)
                    ->where('author', 'Antoine de Saint-Exupéry')
                    ->where('content', 'It is only with the heart that one can see rightly; what is essential is invisible to the eye.')
                    ->whereAllType([
                        'id' => 'integer',
                        'author' => 'string',
                        'content' => 'string',
                    ]);
            })->etc();
        });
});
