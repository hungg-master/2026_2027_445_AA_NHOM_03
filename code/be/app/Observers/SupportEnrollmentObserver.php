<?php

namespace App\Observers;

use App\Models\DangKyLop;
use App\Services\SupportEvents;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class SupportEnrollmentObserver implements ShouldHandleEventsAfterCommit
{
    public function created(DangKyLop $enrollment): void
    {
        app(SupportEvents::class)->enrollmentChanged($enrollment);
    }

    public function updated(DangKyLop $enrollment): void
    {
        if ($enrollment->wasChanged('trang_thai')) {
            app(SupportEvents::class)->enrollmentChanged($enrollment);
        }
    }
}
