<?php

namespace App\Api\Ratings\Controllers;

use App\Api\Ratings\Queries\RatingIndexQuery;
use App\Api\Ratings\Queries\RatingShowQuery;
use App\Api\Ratings\Resources\RatingResource;
use App\Controller;
use Domain\Quotes\Actions\RefreshQuoteAverageScoreAction;
use Domain\Rating\Actions\StoreRatingAction;
use Domain\Rating\Actions\UpdateRatingAction;
use Domain\Rating\Data\StoreRatingData;
use Domain\Rating\Data\UpdateRatingData;
use Domain\Rating\Models\Rating;
use Domain\Users\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RatingController extends Controller
{
    public function index(RatingIndexQuery $quoteQuery): AnonymousResourceCollection
    {
        $quotes = $quoteQuery->paginate();

        return RatingResource::collection($quotes);
    }

    public function show(RatingShowQuery $ratingQuery, int $ratingId): RatingResource
    {
        $rating = $ratingQuery->where('id', $ratingId)->firstOrFail();

        return RatingResource::make($rating);
    }

    public function store(StoreRatingData $data): JsonResponse
    {
        /** @var User $authUser */
        $authUser = auth()->user();

        $rating = (new StoreRatingAction())->__invoke(
            qualifier: $authUser,
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
    public function update(Rating $rating, UpdateRatingData $data): JsonResponse
    {
        $this->authorize('pass', $rating);

        (new UpdateRatingAction())->__invoke(
            rating: $rating,
            data: $data
        );

        return response()->json(['message' => trans('message.updated', ['attribute' => 'rating'])]);
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Rating $rating): JsonResponse
    {
        $this->authorize('pass', $rating);

        $quote = $rating->rateable;

        $rating->delete();

        (new RefreshQuoteAverageScoreAction())->__invoke(quote: $quote);

        return response()->json([
            'message' => trans('message.deleted', ['attribute' => 'rating']),
        ]);
    }
}
