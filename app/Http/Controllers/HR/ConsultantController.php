<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateConsultantRequest;
use App\Repositories\Contracts\ConsultantRepositoryInterface;
use App\Repositories\Contracts\WorkScheduleRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ConsultantController extends Controller
{
    public function __construct(
        protected ConsultantRepositoryInterface $consultantRepository,
        protected WorkScheduleRepositoryInterface $workScheduleRepository
    ) {}

    public function index(): Response
    {
        $consultants = $this->consultantRepository->with(['user', 'workScheduleTemplate'])->all();
        $schedules = $this->workScheduleRepository->all();

        return Inertia::render('HR/Consultants/Index', [
            'consultants' => $consultants,
            'schedules' => $schedules,
        ]);
    }

    public function show(int $id): Response
    {
        $consultant = $this->consultantRepository->with(['user', 'workScheduleTemplate', 'leaves', 'dailyRecords'])->find($id);

        return Inertia::render('HR/Consultants/Show', [
            'consultant' => $consultant,
        ]);
    }

    public function update(UpdateConsultantRequest $request, int $id)
    {
        $this->consultantRepository->update($request->validated(), $id);

        return redirect()->back()->with('success', 'تم تحديث بيانات الاستشاري بنجاح.');
    }

    public function changeStatus(Request $request, int $id)
    {
        $request->validate(['status' => 'required|in:active,inactive,vacation']);
        $this->consultantRepository->updateStatus($id, $request->status);

        return redirect()->back()->with('success', 'تم تغيير حالة الاستشاري بنجاح.');
    }
}
