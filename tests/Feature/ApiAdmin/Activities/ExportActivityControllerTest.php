<?php

use Domain\Exports\ActivityExport;
use Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use function Pest\Laravel\postJson;

beforeEach(function () {
    Excel::fake();
    $this->user = User::factory()->create();

    activity()->log('test log');
    activity()->log('test log 2');
    activity()->log('test log 3');

    giveRoleWithPermission($this->user, 'export activities');

    loginApiAdmin($this->user);
});

it('downloads an export', function () {
    postJson(route('admin.activities.export'))
        ->assertSuccessful();

    Excel::assertDownloaded('activities.xlsx', function (ActivityExport $export) {
        expect($export->collection()->pluck('description'))
            ->toHaveCount(3)
            ->sequence(
                fn ($dataDescription) => $dataDescription->toBe('test log'),
                fn ($dataDescription) => $dataDescription->toBe('test log 2'),
                fn ($dataDescription) => $dataDescription->toBe('test log 3'),
            );

        return true;
    });
});

test('sql queries optimization test', function () {
    DB::enableQueryLog();

    postJson(route('admin.activities.export'))->assertSuccessful();

    Excel::assertDownloaded('activities.xlsx', function (ActivityExport $export) {
        expect($export->collection())->toHaveCount(3);

        return true;
    });

    expect(formatQueries(DB::getQueryLog()))
        ->toHaveCount(4)
        ->sequence(
            fn ($query) => $query->toBe('select * from `permissions`'),
            fn ($query) => $query->toContain('select `roles`.*, `role_has_permissions`.`permission_id` as `pivot_permission_id`, `role_has_permissions`.`role_id` as `pivot_role_id` from `roles` inner join `role_has_permissions` on `roles`.`id` = `role_has_permissions`.`role_id` where `role_has_permissions`.`permission_id` in'),
            fn ($query) => $query->toBe('select `permissions`.*, `model_has_permissions`.`model_id` as `pivot_model_id`, `model_has_permissions`.`permission_id` as `pivot_permission_id`, `model_has_permissions`.`model_type` as `pivot_model_type` from `permissions` inner join `model_has_permissions` on `permissions`.`id` = `model_has_permissions`.`permission_id` where `model_has_permissions`.`model_id` = ? and `model_has_permissions`.`model_type` = ?'),
            fn ($query) => $query->toBe('select `id`, `log_name`, `description`, `subject_type`, `subject_id`, `causer_type`, `causer_id`, `created_at`, `updated_at` from `activity_log`'),
        );

    DB::disableQueryLog();
});
