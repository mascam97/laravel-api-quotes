<?php

namespace Domain\Gifts\Actions;

use Domain\Gifts\Data\StoreGiftData;
use Domain\Gifts\Models\Gift;
use Domain\Pockets\Models\Pocket;
use Domain\Pockets\PocketAggregateRoot;
use Domain\Users\Models\User;

class StoreGiftAction
{
    public function __invoke(StoreGiftData $data, User $senderUser, User $user): Gift
    {
        /** @var Pocket $userPocket */
        $userPocket = Pocket::query()->whereId($user->pocket_id)
            ->select(['id', 'balance', 'currency'])
            ->first();

        // TODO: Throw an error when there is no user pocket

        // TODO: Support conversion between currencies
        if ($userPocket->currency !== $data->currency) {
            // TODO: Create a custom exception to be handed in the controller
            throw new \DomainException('User pocket currency does not match gift currency');
        }

        $gift = new Gift();
        $gift->note = $data->note;
        $gift->amount = $data->amount;
        $gift->currency = $data->currency;
        $gift->senderUser()->associate($senderUser);
        $gift->user()->associate($user);
        $gift->save();

        // TODO: Move ids to uuid for Event Sourcing
        PocketAggregateRoot::retrieve($userPocket->id) /* @phpstan-ignore-line */
            ->addMoney($userPocket, $gift)
            ->persist();

        return $gift;
    }
}
