<?php

namespace App\Api\Transactions\Controllers;

use App\Api\Transactions\Resources\TransactionResource;
use App\Controller;
use Domain\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TransactionsController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        /** @var User $authUser */
        $authUser = $request->user();

        $transactions = $authUser->balanceTransactions();

        return TransactionResource::collection($transactions);
    }
}
