<?php

namespace App\ApiAdmin\Activities\Controllers;

use App\ApiAdmin\Activities\Queries\ActivityIndexQuery;
use App\ApiAdmin\Activities\Resources\ActivityResource;
use App\Controller;
use Domain\Exports\ActivityExport;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Activitylog\Models\Activity;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ActivityController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function index(ActivityIndexQuery $activityQuery): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Activity::class);

        $activities = $activityQuery->paginate();

        return ActivityResource::collection($activities);
    }

    /**
     * @throws AuthorizationException
     */
    public function show(int $activityId): ActivityResource
    {
        $this->authorize('view', Activity::class);

        $query = Activity::query()
            // TODO: Validate there is no many queries to get the activity (by route model binding and query())
            ->where('id', $activityId);

        $activity = QueryBuilder::for($query)
            ->allowedIncludes(['subject', 'causer'])
            ->firstOrFail();

        return ActivityResource::make($activity);
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Activity $activity): JsonResponse
    {
        $this->authorize('delete', $activity);

        $activity->delete();

        return response()->json([
            'message' => trans('message.deleted', ['attribute' => 'activity']),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function export(): BinaryFileResponse
    {
        $this->authorize('export', Activity::class);

        return Excel::download(new ActivityExport(), 'activities.xlsx');
    }
}
