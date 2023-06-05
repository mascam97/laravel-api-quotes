<?php

namespace App\Api\Transactions\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Stripe\Tax\Transaction;

/**
 * @mixin Transaction;
 */
class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array to show only itself.
     *
     * @param  Request  $request
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'currency' => $this->currency,
            'customer' => $this->customer,
        ];
    }
}
