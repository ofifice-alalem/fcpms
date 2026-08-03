<?php

namespace App\Repositories\Contracts;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface TaskDefinitionRepositoryInterface
 * BR-036, BR-036-A, BR-036-B, Revision 1.2
 */
interface TaskDefinitionRepositoryInterface extends RepositoryInterface
{
    /**
     * Revision 1.2 & BR-036: جلب المهام الظاهرة للاستشاري والموقع
     */
    public function getTasksForConsultantAndSite(int $consultantId, int $siteId);

    /**
     * مزامنة تخصيصات المواقع والاستشاريين لمهمة معينة
     */
    public function syncAssignments(int $taskDefinitionId, array $siteIds = [], array $consultantIds = []);
}
