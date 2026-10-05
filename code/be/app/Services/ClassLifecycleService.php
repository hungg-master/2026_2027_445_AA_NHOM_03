<?php

namespace App\Services;

use App\Models\LopHoc;

class ClassLifecycleService
{
    public function cancel(LopHoc $class): void
    {
        if ($class->tinh_trang !== 'da_huy') {
            $class->update(['tinh_trang' => 'da_huy']);
        }
        foreach ($class->buoiHocs()->where('trang_thai', 'scheduled')->get() as $session) {
            $session->update(['trang_thai' => 'cancelled']);
        }
        foreach ($class->dangKyLops()->where('trang_thai', '!=', 'da_huy')->get() as $enrollment) {
            $enrollment->update(['trang_thai' => 'da_huy']);
        }
        $class->phongHop?->update(['trang_thai' => 0]);
    }
}
