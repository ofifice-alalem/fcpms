<?php

namespace App\Services;

use App\Models\DailyRecord;
use App\Models\Site;
use App\Models\SiteVisit;
use App\Models\TaskResponse;
use App\Repositories\Contracts\ConsultantRepositoryInterface;
use App\Repositories\Contracts\DailyRecordRepositoryInterface;
use App\Repositories\Contracts\SiteVisitRepositoryInterface;
use App\Repositories\Contracts\TaskDefinitionRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * ConsultantWorkflowService
 * 
 * 04.2_consultant_workflows.md: إدارة دورة عمل الاستشاري الميداني.
 */
class ConsultantWorkflowService
{
    public function __construct(
        protected CalendarService $calendarService,
        protected PerformanceCalculationService $performanceCalculationService,
        protected DailyRecordRepositoryInterface $dailyRecordRepository,
        protected SiteVisitRepositoryInterface $siteVisitRepository,
        protected TaskDefinitionRepositoryInterface $taskDefinitionRepository,
        protected ConsultantRepositoryInterface $consultantRepository
    ) {}

    /**
     * بدء اليوم أو استئنافه للاستشاري (إنشاء/جلب السجل اليومي).
     */
    public function startOrResumeDay(int $consultantId, ?Carbon $date = null): DailyRecord
    {
        $targetDate = $date ?? Carbon::now();

        // التحقق من صلاحية يوم العمل بالنسبة للاستشاري
        if (!$this->calendarService->isValidWorkDay($consultantId, $targetDate)) {
            // يسمح بالاستئناف ولكن يسجل تنبيه في حال كانت عطلة أو إجازة
        }

        // BR-024 & BR-025: جلب أو إنشاء السجل اليومي تلقائياً
        return $this->dailyRecordRepository->getOrCreateRecord($consultantId, $targetDate);
    }

    /**
     * جلب قائمة المواقع النشطة المتاحة للاستشاري
     */
    public function getAvailableSites(int $consultantId): Collection
    {
        return Site::where('status', 'active')->get();
    }

    /**
     * بدء زيارة موقع ميداني جديد للاستشاري
     */
    public function startSiteVisit(int $consultantId, int $siteId, ?Carbon $date = null): SiteVisit
    {
        $dailyRecord = $this->startOrResumeDay($consultantId, $date);

        return SiteVisit::create([
            'daily_record_id' => $dailyRecord->id,
            'site_id' => $siteId,
            'visit_started_at' => Carbon::now(),
            'status' => 'in_progress',
        ]);
    }

    /**
     * Revision 1.2: جلب المهام المستهدفة لهذه الزيارة والاستشاري والموقع
     */
    public function getSiteTasks(int $siteVisitId): Collection
    {
        $visit = $this->siteVisitRepository->find($siteVisitId);
        $dailyRecord = $visit->dailyRecord;

        return $this->taskDefinitionRepository->getTasksForConsultantAndSite(
            $dailyRecord->consultant_id,
            $visit->site_id
        );
    }

    /**
     * تقديم وتسجيل إجابات مهام الزيارة وإعادة احتساب الأداء فورياً
     */
    public function submitSiteVisit(int $siteVisitId, array $taskResponsesData): SiteVisit
    {
        $visit = $this->siteVisitRepository->find($siteVisitId);

        foreach ($taskResponsesData as $responseData) {
            TaskResponse::updateOrCreate(
                [
                    'site_visit_id' => $siteVisitId,
                    'task_definition_id' => $responseData['task_definition_id'],
                ],
                [
                    'is_completed' => $responseData['is_completed'] ?? true,
                    'response_data' => $responseData['answers'] ?? [],
                ]
            );
        }

        $visit->update([
            'visit_finished_at' => Carbon::now(),
            'status' => 'completed',
        ]);

        // BR-061-A & Revision 1.1: إعادة الاحتساب الفوري للسجل اليومي فور حفظ الإجابات
        $this->performanceCalculationService->recalculateDailyRecord($visit->daily_record_id);

        return $visit->load('responses');
    }

    /**
     * إغلاق وإنهاء يوم العمل
     */
    public function endWorkingDay(int $dailyRecordId): void
    {
        // إعادة احتساب وتثبيت قيم الأداء اليومية النهائية
        $this->performanceCalculationService->recalculateDailyRecord($dailyRecordId);
    }
}
