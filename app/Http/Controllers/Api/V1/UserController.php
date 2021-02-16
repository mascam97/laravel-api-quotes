<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Quote;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserQuotesResource;

class UserController extends Controller
{
    public function index_quotes($user_id)
    {
        $data = UserQuotesResource::collection(Quote::where('user_id', $user_id)->paginate(6));
        return $data;
    }
}
