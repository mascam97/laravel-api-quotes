<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\getJson;
use Services\ExternalApi\ExternalApiService;

beforeEach(function () {
    loginExternalApi();

    Http::preventStrayRequests();
});

it('can show a quote', function () {
    ExternalApiService::fake(['https://example.com/quotes/10' => fn () => Http::response(jsonFixture('ExternalApi/Quote'))]);

    getJson(route('external-api.quotes.show', ['quote' => 10]))
        ->assertOk()
        ->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $data) {
                $data->where('id', 10)
                    ->where('title', 'The Little Prince')
                    ->where('author', 'Antoine de Saint-Exupéry')
                    ->where('content', 'It is only with the heart that one can see rightly; what is essential is invisible to the eye.')
                    ->where('image_url', 'https://picsum.photos/200/300/?random')
                    ->where('year', 1943)
                    ->where('info_url', 'https://en.wikipedia.org/wiki/The_Little_Prince');
            })->etc();
        });
});
