<?php

namespace App\Repositories\Eloquent;

use App\Models\Consultant;
use App\Repositories\Contracts\ConsultantRepositoryInterface;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class ConsultantRepository
 * المعمارية الإجبارية: Prettus Repository Pattern
 * يمنع استدعاء Eloquent المباشر أو Query Builder داخل Controllers.
 */
class ConsultantRepository extends BaseRepository implements ConsultantRepositoryInterface
{
    /**
     * تحديد الموديل الخاص بـ Repository
     */
    public function model(): string
    {
        return Consultant::class;
    }

    /**
     * BR-003: جلب الاستشاريين النشطين
     */
    public function getActiveConsultants()
    {
        return $this->model
            ->where('status', 'active')
            ->with(['user', 'workScheduleTemplate'])
            ->get();
    }

    /**
     * البحث بواسطة الرقم الوظيفي
     */
    public function findByEmployeeNumber(string $employeeNumber)
    {
        return $this->model
            ->where('employee_number', $employeeNumber)
            ->firstOrFail();
    }

    /**
     * BR-003: تحديث حالة الاستشاري
     */
    public function updateStatus(int $consultantId, string $status)
    {
        $consultant = $this->find($consultantId);
        $consultant->update(['status' => $status]);
        return $consultant;
    }
}
