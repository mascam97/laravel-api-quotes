<?php

namespace App\ApiAdmin\Activities\Controllers;

use App\ApiAdmin\Activities\Queries\ActivityIndexQuery;
use App\ApiAdmin\Activities\Queries\ActivityShowQuery;
use App\ApiAdmin\Activities\Resources\ActivityResource;
use App\Controller;
use Domain\Exports\ActivityExport;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/** @authenticated */
class ActivityController extends Controller
{
    /**
     * @bodyParam filter[log_name] string optional Filter by log name. Example: auth
     * @bodyParam include string optional Include relationships. Example: subject,causer
     *
     * @throws AuthorizationException
     */
    public function index(ActivityIndexQuery $activityQuery): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Activity::class);

        $activities = $activityQuery->paginate();

        return ActivityResource::collection($activities);
    }

    /**
     * @bodyParam include string optional Include relationships. Example: subject,causer
     *
     * @throws AuthorizationException
     */
    public function show(ActivityShowQuery $activityQuery, int $activityId): ActivityResource
    {
        $this->authorize('view', Activity::class);

        $activity = $activityQuery->where('id', $activityId)
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
