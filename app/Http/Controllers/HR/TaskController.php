<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskDefinitionRequest;
use App\Models\Site;
use App\Repositories\Contracts\ConsultantRepositoryInterface;
use App\Repositories\Contracts\TaskComponentRepositoryInterface;
use App\Repositories\Contracts\TaskDefinitionRepositoryInterface;
use Inertia\Inertia;
use Inertia\Response;

class TaskController extends Controller
{
    public function __construct(
        protected TaskDefinitionRepositoryInterface $taskDefinitionRepository,
        protected TaskComponentRepositoryInterface $taskComponentRepository,
        protected ConsultantRepositoryInterface $consultantRepository
    ) {}

    public function index(): Response
    {
        $tasks = $this->taskDefinitionRepository->with(['components.options', 'sites', 'consultants'])->all();

        return Inertia::render('HR/Tasks/Index', [
            'tasks' => $tasks,
        ]);
    }

    public function create(): Response
    {
        $sites = Site::where('status', 'active')->get();
        $consultants = $this->consultantRepository->getActiveConsultants();

        return Inertia::render('HR/Tasks/Builder', [
            'sites' => $sites,
            'consultants' => $consultants,
        ]);
    }

    public function store(StoreTaskDefinitionRequest $request)
    {
        $task = $this->taskDefinitionRepository->create($request->only([
            'name', 'description', 'type', 'is_required', 'performance_weight'
        ]));

        foreach ($request->components as $compData) {
            $options = $compData['options'] ?? [];
            $this->taskComponentRepository->createComponentWithOptions($task->id, $compData, $options);
        }

        $this->taskDefinitionRepository->syncAssignments(
            $task->id,
            $request->input('site_ids', []),
            $request->input('consultant_ids', [])
        );

        return redirect()->route('hr.tasks.index')->with('success', 'تم إنشـاء نموذج المهمة بنجاح.');
    }

    public function preview(int $id): Response
    {
        $task = $this->taskDefinitionRepository->with(['components.options', 'sites', 'consultants'])->find($id);

        return Inertia::render('HR/Tasks/Preview', [
            'task' => $task,
        ]);
    }
}
