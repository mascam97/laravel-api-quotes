<?php

namespace App\Api\Ratings\Controllers;

use App\Api\Ratings\Queries\RatingIndexQuery;
use App\Api\Ratings\Resources\RatingResource;
use App\Controller;
use Domain\Quotes\Actions\RefreshQuoteAverageScoreAction;
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
            ->select([
                'id',
                'score',
                'qualifier_id',
                'qualifier_type',
                'qualifier',
                'rateable_id',
                'rateable_type',
                'rateable',
                'created_at',
                'updated_at',
            ])
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

        (new RefreshQuoteAverageScoreAction())->__invoke(quote: $quote);

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

        $quote = $rating->rateable;

        $rating->delete();

        (new RefreshQuoteAverageScoreAction())->__invoke(quote: $quote);

        return response()->json([
            'message' => trans('message.deleted', ['attribute' => 'rating']),
        ]);
    }
}
