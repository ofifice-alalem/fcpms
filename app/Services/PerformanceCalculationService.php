<?php

namespace App\Services;

use App\Repositories\Contracts\DailyRecordRepositoryInterface;
use App\Repositories\Contracts\SiteVisitRepositoryInterface;
use App\Repositories\Contracts\TaskDefinitionRepositoryInterface;
use Carbon\Carbon;

/**
 * PerformanceCalculationService
 * 
 * BR-061-A: إعادة احتساب مؤشرات الأداء اليومية مباشرة بعد كل عملية حفظ تؤثر على المهام.
 * Revision 1.1: Layered Aggregation & Immediate Recalculation Engine.
 */
class PerformanceCalculationService
{
    public function __construct(
        protected DailyRecordRepositoryInterface $dailyRecordRepository,
        protected SiteVisitRepositoryInterface $siteVisitRepository,
        protected TaskDefinitionRepositoryInterface $taskDefinitionRepository
    ) {}

    /**
     * BR-061-A & Revision 1.1:
     * إعادة احتساب وتحديث السجل اليومي فورياً بعد أي عملية حفظ.
     */
    public function recalculateDailyRecord(int $dailyRecordId): void
    {
        $this->updateDailyRecordMetrics($dailyRecordId);
    }

    /**
     * احتساب إجمالي المهام المطلوب إنجازها للاستشاري في اليوم بناءً على الزيارات والمهام المستهدفة.
     */
    public function calculateRequiredTasks(int $consultantId, Carbon $date): int
    {
        $dailyRecord = $this->dailyRecordRepository->getOrCreateRecord($consultantId, $date);
        $visits = $this->siteVisitRepository->getVisitsForDailyRecord($dailyRecord->id);

        $totalRequired = 0;
        foreach ($visits as $visit) {
            $tasks = $this->taskDefinitionRepository->getTasksForConsultantAndSite($consultantId, $visit->site_id);
            $totalRequired += $tasks->where('is_required', true)->count();
        }

        return $totalRequired;
    }

    /**
     * احتساب المهام المكتملة بناءً على إجابات المهام في الزيارات.
     */
    public function calculateCompletedTasks(int $dailyRecordId): int
    {
        return $this->siteVisitRepository->getCompletedTasksCount($dailyRecordId);
    }

    /**
     * تحديث قيم الأداء المشتقة في جدول daily_records
     */
    public function updateDailyRecordMetrics(int $dailyRecordId): void
    {
        $dailyRecord = $this->dailyRecordRepository->find($dailyRecordId);
        $workDate = Carbon::parse($dailyRecord->work_date);

        $requiredTasks = $this->calculateRequiredTasks($dailyRecord->consultant_id, $workDate);
        $completedTasks = $this->calculateCompletedTasks($dailyRecordId);

        $percentage = 0.00;
        if ($requiredTasks > 0) {
            $percentage = round(($completedTasks / $requiredTasks) * 100, 2);
            if ($percentage > 100.00) {
                $percentage = 100.00;
            }
        }

        // BR-061-A: تحديث المؤشرات المشتقة في قاعدة البيانات فورياً
        $this->dailyRecordRepository->updatePerformanceMetrics(
            $dailyRecordId,
            $requiredTasks,
            $completedTasks,
            $percentage
        );
    }
}
