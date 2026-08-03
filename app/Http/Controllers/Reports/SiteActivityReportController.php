<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\SiteVisit;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SiteActivityReportController extends Controller
{
    public function index(): Response
    {
        $visits = QueryBuilder::for(SiteVisit::class)
            ->allowedFilters([
                AllowedFilter::exact('site_id'),
                AllowedFilter::exact('status'),
            ])
            ->allowedSorts(['visit_started_at', 'created_at'])
            ->with(['site', 'dailyRecord.consultant', 'responses'])
            ->paginate(15);

        return Inertia::render('Reports/SiteActivity/Index', [
            'visits' => $visits,
        ]);
    }
}
