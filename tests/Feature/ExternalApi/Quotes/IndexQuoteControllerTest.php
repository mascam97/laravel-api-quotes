<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\getJson;
use Services\ExternalApi\ExternalApiService;

beforeEach(function () {
    loginExternalApi();
});

it('can index', function () {
    ExternalApiService::fake(['https://example.com/quotes' => Http::response(fixture('ExternalApi/Quotes'))]);

    getJson(route('external-api.quotes.index'))
        ->assertOk()
        ->assertJson(function (AssertableJson $json) {
            $json->has('data', 3)
                ->has('data.0', function (AssertableJson $data) {
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
