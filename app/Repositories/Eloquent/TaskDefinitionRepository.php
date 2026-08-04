<?php

namespace App\Repositories\Eloquent;

use App\Models\TaskDefinition;
use App\Repositories\Contracts\TaskDefinitionRepositoryInterface;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class TaskDefinitionRepository
 * المعمارية الإجبارية: Prettus Repository Pattern
 * BR-036, BR-036-A, BR-036-B, Revision 1.2
 */
class TaskDefinitionRepository extends BaseRepository implements TaskDefinitionRepositoryInterface
{
    public function model(): string
    {
        return TaskDefinition::class;
    }

    /**
     * Revision 1.2 & BR-036, BR-036-A, BR-036-B:
     * جلب المهام اليومية النشطة المتاحة للاستشاري والموقع بناءً على قواعد التخصيص.
     */
    public function getTasksForConsultantAndSite(int $consultantId, int $siteId)
    {
        return $this->model
            ->where('status', 'active')
            ->where('type', 'daily')
            ->where(function ($query) use ($consultantId, $siteId) {
                // 1. مهام عامة بالكامل (لا تقتصر على موقع معين ولا استشاري معين)
                $query->where(function ($q) {
                    $q->whereDoesntHave('sites')->whereDoesntHave('consultants');
                })
                // 2. مهام مخصصة للموقع الحالي فقط (ولجميع الاستشاريين)
                ->orWhere(function ($q) use ($siteId) {
                    $q->whereHas('sites', fn($s) => $s->where('sites.id', $siteId))
                      ->whereDoesntHave('consultants');
                })
                // 3. مهام مخصصة للاستشاري الحالي فقط (وفي جميع المواقع)
                ->orWhere(function ($q) use ($consultantId) {
                    $q->whereHas('consultants', fn($c) => $c->where('consultants.id', $consultantId))
                      ->whereDoesntHave('sites');
                })
                // 4. مهام مخصصة للموقع الحالي والاستشاري الحالي معاً (Composite Assignment - BR-036-B)
                ->orWhere(function ($q) use ($consultantId, $siteId) {
                    $q->whereHas('sites', fn($s) => $s->where('sites.id', $siteId))
                      ->whereHas('consultants', fn($c) => $c->where('consultants.id', $consultantId));
                });
            })
            ->with(['components.options'])
            ->get();
    }

    /**
     * مزامنة تخصيصات المواقع والاستشاريين لمهمة معينة
     */
    public function syncAssignments(int $taskDefinitionId, array $siteIds = [], array $consultantIds = [])
    {
        $task = $this->find($taskDefinitionId);
        $task->sites()->sync($siteIds);
        $task->consultants()->sync($consultantIds);

        return $task->load(['sites', 'consultants']);
    }
}
