<?php

namespace App\Api\Ratings\Controllers;

use App\Api\Ratings\Queries\RatingIndexQuery;
use App\Api\Ratings\Resources\RatingResource;
use App\Controller;
use Domain\Quotes\Models\Quote;
use Domain\Rating\Actions\UpdateOrCreateRatingAction;
use Domain\Rating\Data\RatingData;
use Domain\Rating\Models\Rating;
use Domain\Users\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\QueryBuilder;

class RatingController extends Controller
{
    public function index(RatingIndexQuery $quoteQuery): AnonymousResourceCollection
    {
        $quotes = $quoteQuery->paginate();

        return RatingResource::collection($quotes);
    }

    public function show(int $ratingId): RatingResource
    {
        $query = Rating::query()
            ->whereId($ratingId);

        $rating = QueryBuilder::for($query)
            ->allowedIncludes(['qualifier', 'rateable'])
            ->firstOrFail();

        return RatingResource::make($rating);
    }

    public function store(
        Quote $quote,
        RatingData $data,
    ): JsonResponse {
        /** @var User $authUser */
        $authUser = auth()->user();

        $rating = (new UpdateOrCreateRatingAction())->__invoke(
            qualifier: $authUser,
            rateable: $quote,
            data: $data
        );

        $rating->load('rateable');

        return response()->json([
            'message' => trans('message.created', ['attribute' => 'rating']),
            'data' => RatingResource::make($rating),
        ], 201);
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Rating $rating): JsonResponse
    {
        $this->authorize('pass', $rating);

        $rating->delete();

        return response()->json([
            'message' => trans('message.deleted', ['attribute' => 'rating']),
        ]);
    }
}
