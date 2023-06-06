<?php

namespace App\ApiAdmin\Profile\Controllers;

use App\ApiAdmin\Profile\Resources\ProfileResource;
use App\Controller;
use Domain\Users\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request): ProfileResource
    {
        /** @var User $authUser */
        $authUser = $request->user();

        return ProfileResource::make($authUser);
    }

    //    TODO: Add update and destroy functions
}
