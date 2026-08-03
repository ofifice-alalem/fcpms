<?php

namespace App\Providers;

use App\Repositories\Contracts\ConsultantRepositoryInterface;
use App\Repositories\Contracts\DailyRecordRepositoryInterface;
use App\Repositories\Contracts\WorkScheduleRepositoryInterface;
use App\Repositories\Eloquent\ConsultantRepository;
use App\Repositories\Eloquent\DailyRecordRepository;
use App\Repositories\Eloquent\WorkScheduleRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * ربط واجهات Repository بالتنفيذا الخاصة بها (Prettus Repository Pattern)
     */
    public function register(): void
    {
        $this->app->bind(ConsultantRepositoryInterface::class, ConsultantRepository::class);
        $this->app->bind(DailyRecordRepositoryInterface::class, DailyRecordRepository::class);
        $this->app->bind(WorkScheduleRepositoryInterface::class, WorkScheduleRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
