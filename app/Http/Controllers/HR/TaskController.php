<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskDefinitionRequest;
use App\Models\Site;
use App\Models\TaskComponent;
use App\Repositories\Contracts\ConsultantRepositoryInterface;
use App\Repositories\Contracts\TaskComponentRepositoryInterface;
use App\Repositories\Contracts\TaskDefinitionRepositoryInterface;
use Illuminate\Http\Request;
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

        return Inertia::render('HR/Tasks/Create', [
            'sites' => $sites,
            'consultants' => $consultants,
        ]);
    }

    public function store(StoreTaskDefinitionRequest $request)
    {
        $task = $this->taskDefinitionRepository->create($request->only([
            'name', 'description', 'type', 'is_required'
        ]));

        if ($request->has('components')) {
            foreach ($request->components as $compData) {
                $options = $compData['options'] ?? [];
                $this->taskComponentRepository->createComponentWithOptions($task->id, $compData, $options);
            }
        }

        $this->taskDefinitionRepository->syncAssignments(
            $task->id,
            $request->input('site_ids', []),
            $request->input('consultant_ids', [])
        );

        return redirect()->route('hr.tasks.index')->with('success', 'تم إنشـاء نموذج المهمة بنجاح.');
    }

    public function edit(int $id): Response
    {
        $task = $this->taskDefinitionRepository->with(['components.options', 'sites', 'consultants'])->find($id);
        $sites = Site::where('status', 'active')->get();
        $consultants = $this->consultantRepository->getActiveConsultants();

        return Inertia::render('HR/Tasks/Edit', [
            'task' => $task,
            'sites' => $sites,
            'consultants' => $consultants,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $task = $this->taskDefinitionRepository->update(
            $request->only(['name', 'description', 'type', 'is_required']),
            $id
        );

        if ($request->has('components')) {
            // Delete old components to recreate updated structure
            TaskComponent::where('task_definition_id', $id)->delete();
            foreach ($request->components as $compData) {
                $options = $compData['options'] ?? [];
                $this->taskComponentRepository->createComponentWithOptions($id, $compData, $options);
            }
        }

        if ($request->has('site_ids') || $request->has('consultant_ids')) {
            $this->taskDefinitionRepository->syncAssignments(
                $id,
                $request->input('site_ids', []),
                $request->input('consultant_ids', [])
            );
        }

        return redirect()->route('hr.tasks.index')->with('success', 'تم تحديث نموذج المهمة بنجاح.');
    }

    public function changeStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:active,published,disabled,inactive,draft',
        ]);

        $statusMap = [
            'published' => 'active',
            'disabled' => 'inactive',
        ];

        $newStatus = $statusMap[$request->status] ?? $request->status;

        $this->taskDefinitionRepository->update(['status' => $newStatus], $id);

        return redirect()->back()->with('success', $newStatus === 'inactive' ? 'تم تعطيل نموذج المهمة بنجاح.' : 'تم نشر نموذج المهمة وتفعيله بنجاح.');
    }

    public function preview(int $id): Response
    {
        $task = $this->taskDefinitionRepository->with(['components.options', 'sites', 'consultants'])->find($id);

        return Inertia::render('HR/Tasks/Preview', [
            'task' => $task,
        ]);
    }
}
