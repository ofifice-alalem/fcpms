<?php

namespace App\Repositories\Eloquent;

use App\Models\SiteVisit;
use App\Models\TaskResponse;
use App\Repositories\Contracts\SiteVisitRepositoryInterface;
use Prettus\Repository\Eloquent\BaseRepository;

class SiteVisitRepository extends BaseRepository implements SiteVisitRepositoryInterface
{
    public function model(): string
    {
        return SiteVisit::class;
    }

    public function getCompletedTasksCount(int $dailyRecordId): int
    {
        return TaskResponse::whereHas('siteVisit', function ($query) use ($dailyRecordId) {
            $query->where('daily_record_id', $dailyRecordId);
        })
        ->where('is_completed', true)
        ->count();
    }

    public function getVisitsForDailyRecord(int $dailyRecordId)
    {
        return $this->model
            ->where('daily_record_id', $dailyRecordId)
            ->with(['site', 'responses'])
            ->get();
    }
}
