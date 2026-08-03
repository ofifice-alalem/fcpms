<?php

namespace Database\Seeders;

use App\Models\Consultant;
use App\Models\User;
use App\Models\WorkScheduleTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * BR-001: نوعان فقط من الأدوار (hr, consultant).
 * إعداد الصلاحيات الافتراضية وحساب الأدمن الخاص بـ HR.
 */
class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // إعادة تعيين كاش الأدوار والحيادية
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. إنشاء الصلاحيات الأساسية
        $permissions = [
            'access_dashboard',
            'manage_users',
            'manage_tasks',
            'manage_sites',
            'view_reports',
            'fill_daily_tasks',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. إنشاء الدورين (hr, consultant) - BR-001
        $hrRole = Role::firstOrCreate(['name' => 'hr']);
        $consultantRole = Role::firstOrCreate(['name' => 'consultant']);

        // منج الأدوار الصلاحيات المناسبة
        $hrRole->givePermissionTo(Permission::all());
        $consultantRole->givePermissionTo(['access_dashboard', 'fill_daily_tasks']);

        // 3. إنشاء مستخدم HR الافتراضي (admin@fcpms.com)
        $hrUser = User::firstOrCreate(
            ['email' => 'admin@fcpms.com'],
            [
                'name' => 'مسؤول النظام (HR)',
                'password' => Hash::make('password'),
                'role' => 'hr',
                'status' => 'active',
            ]
        );

        $hrUser->assignRole($hrRole);

        // 4. إنشاء قالب عمل افتراضي (Default Standard Schedule) - BR-006
        $defaultSchedule = WorkScheduleTemplate::firstOrCreate(
            ['name' => 'جدول العمل الأسبوعي القياسي'],
            ['description' => 'جدول عمل قياسي من الأحد إلى الخميس']
        );

        $workingDays = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday'];
        $allDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        foreach ($allDays as $day) {
            $defaultSchedule->days()->firstOrCreate(
                ['day_of_week' => $day],
                ['is_working_day' => in_array($day, $workingDays)]
            );
        }

        // 5. إنشاء حساب استشاري افتراضي للتجربة
        $consultantUser = User::firstOrCreate(
            ['email' => 'consultant@fcpms.com'],
            [
                'name' => 'استشاري تجريبي',
                'password' => Hash::make('password'),
                'role' => 'consultant',
                'status' => 'active',
            ]
        );

        $consultantUser->assignRole($consultantRole);

        Consultant::firstOrCreate(
            ['user_id' => $consultantUser->id],
            [
                'employee_number' => 'EMP-1001',
                'full_name' => 'استشاري تجريبي ميداني',
                'phone' => '+218910000000',
                'specialization' => 'الهندسة المدنية',
                'work_schedule_template_id' => $defaultSchedule->id,
                'status' => 'active',
            ]
        );
    }
}
