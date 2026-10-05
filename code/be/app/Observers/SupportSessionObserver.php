<?php

namespace App\Observers;

use App\Models\BuoiHoc;
use App\Services\SupportEvents;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class SupportSessionObserver implements ShouldHandleEventsAfterCommit
{
    public function updated(BuoiHoc $session): void
    {
        if ($session->wasChanged(['trang_thai', 'thoi_gian_bat_dau', 'thoi_gian_ket_thuc'])) {
            app(SupportEvents::class)->sessionChanged($session);
        }
    }
}
