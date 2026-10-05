<?php

namespace App\Providers;

use App\Console\Commands\RemindLessons;
use App\Models\BuoiHoc;
use App\Models\DangKyLop;
use App\Observers\SupportEnrollmentObserver;
use App\Observers\SupportSessionObserver;
use Illuminate\Support\ServiceProvider;

class SupportFeatureProvider extends ServiceProvider
{
    public function boot(): void
    {
        DangKyLop::observe(SupportEnrollmentObserver::class);
        BuoiHoc::observe(SupportSessionObserver::class);
        if ($this->app->runningInConsole()) {
            $this->commands([RemindLessons::class]);
        }
    }
}
