<?php

namespace App\Providers;

use App\Contracts\Repositories\MedicalRecordRepositoryInterface;
use App\Contracts\Services\DashboardServiceInterface;
use App\Contracts\Services\MedicalRecordServiceInterface;
use App\Repositories\MedicalRecordRepository;
use App\Services\DashboardService;
use App\Services\MedicalRecordService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(
            MedicalRecordRepositoryInterface::class,
            MedicalRecordRepository::class
        );

        // Service bindings
        $this->app->bind(
            MedicalRecordServiceInterface::class,
            MedicalRecordService::class
        );

        $this->app->bind(
            DashboardServiceInterface::class,
            DashboardService::class
        );
    }

    public function boot(): void {}
}
