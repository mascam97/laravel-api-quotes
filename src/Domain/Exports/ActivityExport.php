<?php

namespace Domain\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Spatie\Activitylog\Models\Activity;

class ActivityExport implements FromCollection
{
    use Exportable;

    private array $headers = [
        'id',
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'created_at',
        'updated_at',
    ];

    public function headings(): array
    {
        return $this->headers;
    }

    public function collection(): Collection|array|\Illuminate\Support\Collection
    {
        // TODO: Validate performance when there are many data
        return Activity::all();
    }
}
