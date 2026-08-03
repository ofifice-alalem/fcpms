<?php

namespace App\Repositories\Contracts;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface ConsultantRepositoryInterface
 * المعمارية الإجبارية: Prettus Repository Pattern
 * BR-002, BR-003, BR-005: إدارة بيانات الاستشاريين
 */
interface ConsultantRepositoryInterface extends RepositoryInterface
{
    /**
     * جلب الاستشاريين النشطين فقط
     */
    public function getActiveConsultants();

    /**
     * البحث عن استشاري بواسطة الرقم الوظيفي
     */
    public function findByEmployeeNumber(string $employeeNumber);

    /**
     * تحديث حالة الاستشاري (Active, Inactive, Vacation)
     */
    public function updateStatus(int $consultantId, string $status);
}
