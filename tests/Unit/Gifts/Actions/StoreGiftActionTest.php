<?php

use Domain\Gifts\Actions\StoreGiftAction;
use Domain\Gifts\Data\StoreGiftData;
use Domain\Gifts\Models\Gift;
use Domain\Pockets\Models\Pocket;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\assertEquals;

beforeEach(function () {
    $this->pocket = Pocket::factory()->create(['balance' => 0, 'currency' => 'USD']);
    $this->user = User::factory()->create(['pocket_id' => $this->pocket->id]);

    $this->senderUser = User::factory()->create();
});

it('can create a gift and user pocket received the money', function () {
    $data = new StoreGiftData(note: 'Thanks for your work', amount: 1_500_00, currency: 'USD');

    $gift = (new StoreGiftAction())->__invoke(data: $data, senderUser: $this->senderUser, user: $this->user);

    expect($gift) /* @phpstan-ignore-line */
        ->senderUser->toEqual($this->senderUser)
        ->user->toEqual($this->user)
        ->note->toEqual('Thanks for your work')
        ->amount->toEqual(1_500_00)
        ->currency->toEqual('USD');

    $this->pocket->refresh();

    assertEquals(1_500_00, $this->pocket->balance);
});

it('cannot create a gift with different currency', function () {
    $data = new StoreGiftData(note: 'Thanks for your work', amount: 1_500_00, currency: 'MXN');

    try {
        (new StoreGiftAction())->__invoke(data: $data, senderUser: $this->senderUser, user: $this->user);
    } catch (DomainException $e) {
        assertEquals('User pocket currency does not match gift currency', $e->getMessage());
    }

    assertEquals(0, Gift::query()->count());

    $this->pocket->refresh();

    assertEquals(0, $this->pocket->balance);
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();

    $data = new StoreGiftData(note: 'Thanks for your work', amount: 1_500_00, currency: 'USD');

    (new StoreGiftAction())->__invoke(data: $data, senderUser: $this->senderUser, user: $this->user);

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(9)
        ->sequence(
            fn ($query) => $query->toBe('select `id`, `balance`, `currency` from `pockets` where `id` = ? and `pockets`.`deleted_at` is null limit 1'),
            fn ($query) => $query->toBe('insert into `gifts` (`note`, `amount`, `currency`, `sender_user_id`, `user_id`, `updated_at`, `created_at`) values (?, ?, ?, ?, ?, ?, ?)'),

            // TODO: Many queries are executed by EventSourcing, look for a way to reduce them
            fn ($query) => $query->toBe('select * from `snapshots` where `aggregate_uuid` = ? order by `id` desc limit 1'),
            fn ($query) => $query->toBe('select * from `stored_events` where `aggregate_uuid` = ? order by `id` asc'),
            fn ($query) => $query->toBe('select max(`aggregate_version`) as aggregate from `stored_events` where `aggregate_uuid` = ?'),
            fn ($query) => $query->toBe('insert into `stored_events` (`event_properties`, `aggregate_uuid`, `aggregate_version`, `event_version`, `event_class`, `meta_data`, `created_at`) values (?, ?, ?, ?, ?, ?, ?)'),
            fn ($query) => $query->toBe('update `stored_events` set `meta_data` = ? where `id` = ?'),
            fn ($query) => $query->toBe('select * from `pockets` where `pockets`.`id` = ? and `pockets`.`deleted_at` is null limit 1'),
            fn ($query) => $query->toBe('update `pockets` set `balance` = ?, `pockets`.`updated_at` = ? where `id` = ?'),
        );

    DB::disableQueryLog();
});
