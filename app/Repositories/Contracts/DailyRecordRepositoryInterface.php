<?php

namespace App\Repositories\Contracts;

use Carbon\Carbon;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface DailyRecordRepositoryInterface
 * BR-024: لكل استشاري سجل يومي واحد لكل تاريخ.
 * Revision 1.1: السجل اليومي يحوي البيانات المشتقة للأداء (Performance Indicators).
 */
interface DailyRecordRepositoryInterface extends RepositoryInterface
{
    /**
     * BR-024 & BR-025: الحصول على السجل اليومي أو إنشاؤه تلقائياً
     */
    public function getOrCreateRecord(int $consultantId, Carbon $date);

    /**
     * Revision 1.1 & BR-061-A: تحديث مؤشرات الأداء اليومية مباشرة
     */
    public function updatePerformanceMetrics(int $dailyRecordId, int $requiredTasks, int $completedTasks, float $percentage);

    /**
     * جلب سجلات فترة زمنية لاستشاري محدد للتقارير
     */
    public function getRecordsForPeriod(int $consultantId, Carbon $startDate, Carbon $endDate);
}
