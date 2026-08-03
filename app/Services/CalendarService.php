<?php

namespace App\Services;

use App\Models\Consultant;
use App\Models\OfficialHoliday;
use App\Repositories\Contracts\ConsultantRepositoryInterface;
use Carbon\Carbon;

/**
 * CalendarService
 * 
 * BR-008: التحقق من أيام العمل من جدول العمل الخاص بالاستشاري.
 * BR-012 & BR-013: التحقق من العطل الرسمية.
 * BR-015 & BR-016: التحقق من إجازات الاستشاريين.
 * BR-018: تحديد أيام العمل الفعلية المستحقة للغياب أو العمل.
 */
class CalendarService
{
    public function __construct(
        protected ConsultantRepositoryInterface $consultantRepository
    ) {}

    /**
     * BR-008: هل اليوم هو يوم عمل محدد في جدول الاستشاري؟
     */
    public function isWorkingDay(int $consultantId, Carbon $date): bool
    {
        $consultant = $this->consultantRepository->find($consultantId);
        $template = $consultant->workScheduleTemplate;

        if (!$template) {
            // افتراض أن الأيام من الأحد إلى الخميس هي أيام عمل إذا لم يوجد قالب
            $dayName = strtolower($date->format('l'));
            return !in_array($dayName, ['friday', 'saturday']);
        }

        $dayName = strtolower($date->format('l'));
        $scheduleDay = $template->days()->where('day_of_week', $dayName)->first();

        return $scheduleDay ? (bool) $scheduleDay->is_working_day : false;
    }

    /**
     * BR-012 & BR-013: هل التاريخ يقع ضمن عطلة رسمية؟
     */
    public function isOfficialHoliday(Carbon $date): bool
    {
        $dateStr = $date->toDateString();
        return OfficialHoliday::whereDate('start_date', '<=', $dateStr)
            ->whereDate('end_date', '>=', $dateStr)
            ->exists();
    }

    /**
     * BR-015 & BR-016: هل الاستشاري في إجازة في هذا التاريخ؟
     */
    public function isOnVacation(int $consultantId, Carbon $date): bool
    {
        $consultant = $this->consultantRepository->find($consultantId);
        $dateStr = $date->toDateString();

        return $consultant->leaves()
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $dateStr)
            ->whereDate('end_date', '>=', $dateStr)
            ->exists();
    }

    /**
     * BR-018: هل اليوم يُعد يوم عمل فعلي ومطالب به الاستشاري؟
     * (يكون يوم عمل في القالب، وليس عطلة رسمية، وليس إجازة استشاري)
     */
    public function isValidWorkDay(int $consultantId, Carbon $date): bool
    {
        if ($this->isOfficialHoliday($date)) {
            return false;
        }

        if ($this->isOnVacation($consultantId, $date)) {
            return false;
        }

        return $this->isWorkingDay($consultantId, $date);
    }
}
