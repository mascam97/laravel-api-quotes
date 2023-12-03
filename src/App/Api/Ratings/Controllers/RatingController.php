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
use Support\Metadata\GetQueryMetaDataAction;

/** @authenticated */
class RatingController extends Controller
{
    /**
     * @bodyParam filter[qualifier_type] string Filter by qualifier type Example: App\Models\User
     * @bodyParam filter[rateable_type] string Filter by rateable type Example: App\Models\Quote
     * @bodyParam include string Include qualifier and rateable Example: qualifier,rateable
     * @bodyParam sort string Sort by fields Example: id,created_at
     */
    public function index(RatingIndexQuery $ratingQuery): AnonymousResourceCollection
    {
        $quotes = $ratingQuery
            ->jsonPaginate()
            ->withQueryString();

        return RatingResource::collection($quotes)
            ->additional(['meta' => (new GetQueryMetaDataAction())->__invoke($ratingQuery->getQuery())]);
    }

    /**
     * @bodyParam include string Include qualifier and rateable Example: qualifier,rateable
     */
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
