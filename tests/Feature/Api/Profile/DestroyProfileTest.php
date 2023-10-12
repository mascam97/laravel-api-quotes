<?php

use Domain\Users\Factories\UserFactory;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;

beforeEach(function () {
    $this->user = User::factory()->create(['password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi']);

    (new UserFactory)->setAmount(4)->create();

    loginApi($this->user);
});

it('can destroy user', function () {
    deleteJson(route('api.profile.destroy', ['password' => 'password']))
        ->assertOk()
        ->assertJson(function (AssertableJson $json) {
            $json->where('message', 'The user was deleted successfully');
        });

    assertSoftDeleted($this->user);
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();
    deleteJson(route('api.profile.destroy'), ['password' => 'password'])->assertOk();

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(3)
        ->sequence(
            fn ($query) => $query->toBe('delete from `quotes` where `quotes`.`user_id` = ? and `quotes`.`user_id` is not null'),
            fn ($query) => $query->toBe('delete `quotes` from `quotes` inner join `ratings` on `quotes`.`id` = `ratings`.`rateable_id` where `ratings`.`qualifier_id` = ? and `ratings`.`qualifier_type` = ? and `ratings`.`rateable_type` = ? and `ratings`.`qualifier_type` = ?'),
            fn ($query) => $query->toBe('update `users` set `deleted_at` = ?, `users`.`updated_at` = ? where `id` = ?'),
        );

    DB::disableQueryLog();
});
