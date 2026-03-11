<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Import Interfaces
use App\Repositories\Interfaces\MachineRepositoryInterface;
use App\Repositories\Interfaces\PaperMachineReportRepositoryInterface;
use App\Repositories\Interfaces\PaperMachineRollRepositoryInterface;
use App\Repositories\Interfaces\PaperMachineProblemRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\QualityTestRepositoryInterface;
use App\Repositories\Interfaces\WinderLogRepositoryInterface;

// Import Repositories
use App\Repositories\MachineRepository;
use App\Repositories\PaperMachineReportRepository;
use App\Repositories\PaperMachineRollRepository;
use App\Repositories\PaperMachineProblemRepository;
use App\Repositories\UserRepository;
use App\Repositories\QualityTestRepository;
use App\Repositories\WinderLogRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repository Bindings
        $this->app->bind(MachineRepositoryInterface::class, MachineRepository::class);
        $this->app->bind(PaperMachineReportRepositoryInterface::class, PaperMachineReportRepository::class);
        $this->app->bind(PaperMachineRollRepositoryInterface::class, PaperMachineRollRepository::class);
        $this->app->bind(PaperMachineProblemRepositoryInterface::class, PaperMachineProblemRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(QualityTestRepositoryInterface::class, QualityTestRepository::class);
        $this->app->bind(WinderLogRepositoryInterface::class, WinderLogRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}