<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateConsultantRequest;
use App\Models\User;
use App\Repositories\Contracts\ConsultantRepositoryInterface;
use App\Repositories\Contracts\WorkScheduleRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

    public function store(Request $request)
    {
        $request->validate([
            'employee_number' => 'required|string|max:50|unique:consultants,employee_number',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'specialization' => 'nullable|string|max:100',
            'work_schedule_template_id' => 'nullable|exists:work_schedule_templates,id',
            'status' => 'required|in:active,inactive,vacation',
        ]);

        $user = User::create([
            'name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make('password123'),
            'role' => 'consultant',
            'status' => $request->status === 'active' ? 'active' : 'inactive',
        ]);

        if (method_exists($user, 'assignRole')) {
            try {
                $user->assignRole('consultant');
            } catch (\Throwable $e) {
                // Ignore if Spatie role not defined
            }
        }

        $this->consultantRepository->create([
            'user_id' => $user->id,
            'employee_number' => $request->employee_number,
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'specialization' => $request->specialization,
            'work_schedule_template_id' => $request->work_schedule_template_id,
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'تم إضافة الاستشاري الميداني وإنشاء حسابه بنجاح.');
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
