<?php

namespace App\Repositories\Eloquent;

use App\Models\DailyRecord;
use App\Repositories\Contracts\DailyRecordRepositoryInterface;
use Carbon\Carbon;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class DailyRecordRepository
 * المعمارية الإجبارية: Prettus Repository Pattern
 * BR-024, BR-025, Revision 1.1 (Performance Engine).
 */
class DailyRecordRepository extends BaseRepository implements DailyRecordRepositoryInterface
{
    public function model(): string
    {
        return DailyRecord::class;
    }

    /**
     * BR-024 & BR-025: جلب أو إنشاء السجل اليومي بشكل آمن وتلقائي
     */
    public function getOrCreateRecord(int $consultantId, Carbon $date)
    {
        return $this->model->firstOrCreate(
            [
                'consultant_id' => $consultantId,
                'work_date' => $date->toDateString(),
            ],
            [
                'required_daily_tasks' => 0,
                'completed_daily_tasks' => 0,
                'completion_percentage' => 0.00,
            ]
        );
    }

    /**
     * Revision 1.1 & BR-061-A: تحديث مؤشرات الأداء المشتقة فورياً
     */
    public function updatePerformanceMetrics(int $dailyRecordId, int $requiredTasks, int $completedTasks, float $percentage)
    {
        $record = $this->find($dailyRecordId);
        $record->update([
            'required_daily_tasks' => $requiredTasks,
            'completed_daily_tasks' => $completedTasks,
            'completion_percentage' => $percentage,
        ]);
        return $record;
    }

    /**
     * جلب سجلات فترة زمنية لاستشاري للتقارير
     */
    public function getRecordsForPeriod(int $consultantId, Carbon $startDate, Carbon $endDate)
    {
        return $this->model
            ->where('consultant_id', $consultantId)
            ->whereBetween('work_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('work_date', 'asc')
            ->get();
    }
}
