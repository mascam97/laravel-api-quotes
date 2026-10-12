<?php

declare(strict_types=1);

namespace App\Api\Users\Controllers;

use App\Controller;
use Domain\Users\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SendEmailVerificationController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => trans('message.email_already_verified')]);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => trans('message.email_verification_sent'),
        ], 202);
    }
}
