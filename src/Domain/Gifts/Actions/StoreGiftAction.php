<?php

namespace Domain\Gifts\Actions;

use Domain\Gifts\Data\StoreGiftData;
use Domain\Gifts\Models\Gift;
use Domain\Pockets\Models\Pocket;
use Domain\Users\Models\User;
use Money\Currency;
use Money\Money;

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

        $userMoney = new Money($userPocket->balance, new Currency($userPocket->currency)); /* @phpstan-ignore-line */
        $giftMoney = new Money($data->amount, new Currency($data->currency)); /* @phpstan-ignore-line */

        $userPocket->balance = (int) $userMoney->add($giftMoney)->getAmount();
        $userPocket->update();

        $gift = new Gift();
        $gift->note = $data->note;
        $gift->amount = $data->amount;
        $gift->currency = $data->currency;
        $gift->senderUser()->associate($senderUser);
        $gift->user()->associate($user);
        $gift->save();

        return $gift;
    }
}
