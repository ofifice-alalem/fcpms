<?php

namespace App\Http\Controllers\Consultant;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitSiteVisitRequest;
use App\Models\SiteVisit;
use App\Models\TaskDefinition;
use App\Services\ConsultantWorkflowService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DailyWorkController extends Controller
{
    public function __construct(
        protected ConsultantWorkflowService $consultantWorkflowService
    ) {}

    public function getSites(): Response
    {
        $user = auth()->user();
        $sites = $this->consultantWorkflowService->getAvailableSites($user->consultant->id);

        return Inertia::render('Consultant/DailyWork/Sites', [
            'sites' => $sites,
        ]);
    }

    public function startVisit(Request $request)
    {
        $request->validate(['site_id' => 'required|exists:sites,id']);
        $user = auth()->user();

        $visit = $this->consultantWorkflowService->startSiteVisit(
            $user->consultant->id,
            $request->site_id,
            Carbon::now()
        );

        return redirect()->route('consultant.visit.tasks', ['visitId' => $visit->id]);
    }

    public function getTasks(int $visitId): Response
    {
        $visit = SiteVisit::with(['site', 'responses'])->findOrFail($visitId);
        $tasks = $this->consultantWorkflowService->getSiteTasks($visitId);
        $onDemandTasks = TaskDefinition::where('type', 'on_demand')
            ->where('status', 'active')
            ->with('components.options')
            ->get();

        return Inertia::render('Consultant/DailyWork/Tasks', [
            'visitId' => $visitId,
            'site' => $visit->site,
            'tasks' => $tasks,
            'onDemandTasks' => $onDemandTasks,
            'savedResponses' => $visit->responses,
        ]);
    }

    public function submitVisit(SubmitSiteVisitRequest $request)
    {
        $this->consultantWorkflowService->submitSiteVisit(
            $request->site_visit_id,
            $request->responses
        );

        return redirect()->route('consultant.dashboard')->with('success', 'تم حفظ الزيارة وإعادة احتساب نسبة الإنجاز بنجاح.');
    }
}
