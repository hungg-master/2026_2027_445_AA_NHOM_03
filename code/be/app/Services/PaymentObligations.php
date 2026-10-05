<?php

namespace App\Services;

use App\Models\DangKyLop;
use Illuminate\Support\Facades\DB;

class PaymentObligations
{
    public function sync(DangKyLop $enrollment): void
    {
        SupportTransaction::run(function () use ($enrollment) {
            $class = $enrollment->lopHoc;
            if (! $class) {
                return;
            }
            $existing = DB::table('payment_obligations')->where('id_dang_ky_lop', $enrollment->id)->first();
            $active = in_array($enrollment->trang_thai, ['da_xac_nhan', 'da_thanh_toan'], true);
            if (! $active) {
                if ($existing && $existing->status !== 'paid') {
                    DB::table('payment_obligations')->where('id', $existing->id)->update(['status' => 'cancelled', 'updated_at' => now()]);
                }

                return;
            }
            if ($existing) {
                if ($existing->status === 'cancelled') {
                    DB::table('payment_obligations')->where('id', $existing->id)->update(['status' => 'pending', 'updated_at' => now()]);
                }

                return;
            }
            DB::table('payment_obligations')->insert(['id_dang_ky_lop' => $enrollment->id, 'id_hoc_vien' => $enrollment->id_hoc_vien,
                'id_giao_vien' => $class->id_giao_vien, 'id_lop_hoc' => $class->id, 'amount' => (int) round((float) $class->hoc_phi),
                'currency' => 'VND', 'status' => 'pending', 'created_at' => now(), 'updated_at' => now()]);
        });
    }
}
