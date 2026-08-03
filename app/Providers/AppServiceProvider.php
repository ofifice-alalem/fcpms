<?php

namespace App\Providers;

use App\Repositories\Contracts\ConsultantRepositoryInterface;
use App\Repositories\Contracts\DailyRecordRepositoryInterface;
use App\Repositories\Contracts\TaskComponentRepositoryInterface;
use App\Repositories\Contracts\TaskDefinitionRepositoryInterface;
use App\Repositories\Contracts\WorkScheduleRepositoryInterface;
use App\Repositories\Eloquent\ConsultantRepository;
use App\Repositories\Eloquent\DailyRecordRepository;
use App\Repositories\Eloquent\TaskComponentRepository;
use App\Repositories\Eloquent\TaskDefinitionRepository;
use App\Repositories\Eloquent\WorkScheduleRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * ربط واجهات Repositories بالـ Eloquent implementations الخاصة بها.
     */
    public function register(): void
    {
        $this->app->bind(ConsultantRepositoryInterface::class, ConsultantRepository::class);
        $this->app->bind(DailyRecordRepositoryInterface::class, DailyRecordRepository::class);
        $this->app->bind(WorkScheduleRepositoryInterface::class, WorkScheduleRepository::class);
        $this->app->bind(TaskDefinitionRepositoryInterface::class, TaskDefinitionRepository::class);
        $this->app->bind(TaskComponentRepositoryInterface::class, TaskComponentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
