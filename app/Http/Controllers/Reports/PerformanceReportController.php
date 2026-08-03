<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\DailyRecord;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PerformanceReportController extends Controller
{
    public function index(): Response
    {
        // Spatie QueryBuilder لمعالجة الفلاتر المتقدمة والترتيب في تقارير الأداء
        $records = QueryBuilder::for(DailyRecord::class)
            ->allowedFilters([
                AllowedFilter::exact('consultant_id'),
                AllowedFilter::scope('work_date'),
                'completion_percentage',
            ])
            ->allowedSorts(['work_date', 'completion_percentage', 'completed_daily_tasks'])
            ->with(['consultant.user'])
            ->paginate(15);

        return Inertia::render('Reports/Performance/Index', [
            'records' => $records,
        ]);
    }
}
