<?php

namespace App\Http\Controllers\Consultant;

use App\Http\Controllers\Controller;
use App\Services\ConsultantWorkflowService;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        protected ConsultantWorkflowService $consultantWorkflowService
    ) {}

    public function show(): Response
    {
        $user = auth()->user();
        $consultant = $user->consultant;

        $dailyRecord = $this->consultantWorkflowService->startOrResumeDay($consultant->id, Carbon::now());

        return Inertia::render('Consultant/Dashboard', [
            'consultant' => $consultant,
            'dailyRecord' => $dailyRecord->load('siteVisits.site'),
        ]);
    }
}
