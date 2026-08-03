<?php

namespace App\Repositories\Contracts;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface WorkScheduleRepositoryInterface
 * BR-006, BR-008: إدارة جداول العمل وأيام الأسبوع
 */
interface WorkScheduleRepositoryInterface extends RepositoryInterface
{
    /**
     * جلب كافة القوالب مع أيامها
     */
    public function getAllTemplatesWithDays();

    /**
     * إضافة أو تحديث جدول عمل بقوالبه وأيامه
     */
    public function syncTemplateDays(int $templateId, array $days);
}
