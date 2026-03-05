<?php

namespace App\Providers;

use App\Models\PaperMachineReport;
use App\Models\QualityTest;
use App\Models\WinderLog;
use App\Policies\PaperMachineReportPolicy;
use App\Policies\QualityTestPolicy;
use App\Policies\WinderLogPolicy;
use App\Repositories\Interfaces\MachineRepositoryInterface;
use App\Repositories\Interfaces\PaperMachineProblemRepositoryInterface;
use App\Repositories\Interfaces\PaperMachineReportRepositoryInterface;
use App\Repositories\Interfaces\PaperMachineRollRepositoryInterface;
use App\Repositories\Interfaces\QualityTestRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\WinderLogRepositoryInterface;
use App\Repositories\MachineRepository;
use App\Repositories\PaperMachineProblemRepository;
use App\Repositories\PaperMachineReportRepository;
use App\Repositories\PaperMachineRollRepository;
use App\Repositories\QualityTestRepository;
use App\Repositories\UserRepository;
use App\Repositories\WinderLogRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class,                 UserRepository::class);
        $this->app->bind(MachineRepositoryInterface::class,              MachineRepository::class);
        $this->app->bind(PaperMachineReportRepositoryInterface::class,   PaperMachineReportRepository::class);
        $this->app->bind(PaperMachineRollRepositoryInterface::class,     PaperMachineRollRepository::class);
        $this->app->bind(PaperMachineProblemRepositoryInterface::class,  PaperMachineProblemRepository::class);
        $this->app->bind(QualityTestRepositoryInterface::class,          QualityTestRepository::class);
        $this->app->bind(WinderLogRepositoryInterface::class,            WinderLogRepository::class);
    }

    public function boot(): void
    {
        Gate::policy(PaperMachineReport::class, PaperMachineReportPolicy::class);
        Gate::policy(QualityTest::class,        QualityTestPolicy::class);
        Gate::policy(WinderLog::class,          WinderLogPolicy::class);
    }
}
