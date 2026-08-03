<?php

namespace App\Repositories\Eloquent;

use App\Models\WorkScheduleTemplate;
use App\Repositories\Contracts\WorkScheduleRepositoryInterface;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class WorkScheduleRepository
 * BR-006, BR-008
 */
class WorkScheduleRepository extends BaseRepository implements WorkScheduleRepositoryInterface
{
    public function model(): string
    {
        return WorkScheduleTemplate::class;
    }

    /**
     * BR-006: جلب قوالب العمل
     */
    public function getAllTemplatesWithDays()
    {
        return $this->model->with('days')->get();
    }

    /**
     * BR-008: تحديث أيام العمل في القالب
     */
    public function syncTemplateDays(int $templateId, array $daysData)
    {
        $template = $this->find($templateId);
        
        foreach ($daysData as $dayData) {
            $template->days()->updateOrCreate(
                ['day_of_week' => $dayData['day_of_week']],
                ['is_working_day' => $dayData['is_working_day']]
            );
        }

        return $template->load('days');
    }
}
