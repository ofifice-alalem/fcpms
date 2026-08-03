<?php

namespace App\Repositories\Contracts;

use Prettus\Repository\Contracts\RepositoryInterface;

interface SiteVisitRepositoryInterface extends RepositoryInterface
{
    public function getCompletedTasksCount(int $dailyRecordId): int;
    public function getVisitsForDailyRecord(int $dailyRecordId);
}
