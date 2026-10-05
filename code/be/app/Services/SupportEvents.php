<?php

namespace App\Services;

use App\Mail\BusinessEventMail;
use App\Models\BuoiHoc;
use App\Models\DangKyLop;
use App\Models\GiaoVien;
use App\Models\HocVien;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SupportEvents
{
    public function enrollmentChanged(DangKyLop $enrollment): void
    {
        app(PaymentObligations::class)->sync($enrollment);
        $class = $enrollment->lopHoc;
        if (! $class) {
            return;
        }
        $event = $enrollment->trang_thai === 'da_huy' ? 'enrollment_cancelled' : 'enrollment';
        $body = 'Đăng ký lớp #'.$class->id.' có trạng thái '.$enrollment->trang_thai.'. Học phí được theo dõi riêng trong tài khoản.';
        $version = $enrollment->getAttribute('updated_at')?->format('YmdHis.u') ?? 'created';
        foreach ([HocVien::find($enrollment->id_hoc_vien), GiaoVien::find($class->id_giao_vien)] as $actor) {
            if ($actor) {
                $this->notify($actor->email, $event, $body, $event.':'.$enrollment->id.':'.$version.':'.get_class($actor).':'.$actor->id);
            }
        }
    }

    public function sessionChanged(BuoiHoc $session): void
    {
        $class = $session->lopHoc;
        if (! $class) {
            return;
        }
        $body = 'Buổi học #'.$session->id.' của lớp #'.$class->id.' cập nhật: '.$session->trang_thai.'. Bắt đầu '.$session->thoi_gian_bat_dau->toIso8601String().'.';
        $version = $session->updated_at?->format('YmdHis.u') ?? 'created';
        foreach ($this->sessionActors($session) as $actor) {
            $this->notify($actor->email, 'session_changed', $body, 'session:'.$session->id.':'.$version.':'.get_class($actor).':'.$actor->id);
        }
    }

    public function trialChanged(int $id): void
    {
        $booking = DB::table('trial_bookings')->find($id);
        if (! $booking) {
            return;
        }
        foreach ([isset($booking->id_hoc_vien) ? HocVien::find($booking->id_hoc_vien) : null,
            isset($booking->id_giao_vien) ? GiaoVien::find($booking->id_giao_vien) : null] as $actor) {
            if ($actor) {
                $this->notify($actor->email, 'trial_changed', 'Yêu cầu học thử #'.$id.' có trạng thái '.$booking->status.'.',
                    'trial:'.$id.':'.$booking->status.':'.$booking->updated_at.':'.get_class($actor).':'.$actor->id);
            }
        }
    }

    public function paymentConfirmed(int $id): void
    {
        $transaction = DB::table('payment_transactions')->find($id);
        if (! $transaction || $transaction->status !== 'confirmed') {
            return;
        }
        $obligation = DB::table('payment_obligations')->find($transaction->obligation_id);
        foreach ([HocVien::find($obligation->id_hoc_vien), GiaoVien::find($obligation->id_giao_vien)] as $actor) {
            if ($actor) {
                $this->notify($actor->email, 'test_payment_confirmed', 'Giao dịch THỬ NGHIỆM #'.$id.' được quản trị viên đối soát: '.$transaction->amount.' VND. Đây không phải xác nhận thu tiền thực.',
                    'payment:'.$id.':'.get_class($actor).':'.$actor->id);
            }
        }
    }

    public function remind(BuoiHoc $session): void
    {
        foreach ($this->sessionActors($session) as $actor) {
            $this->notify($actor->email, 'session_reminder', 'Nhắc lịch buổi học #'.$session->id.' bắt đầu '.$session->thoi_gian_bat_dau->toIso8601String().'.',
                'reminder:'.$session->id.':'.$session->thoi_gian_bat_dau->timestamp.':'.get_class($actor).':'.$actor->id);
        }
    }

    public function retryPending(): void
    {
        foreach (DB::table('business_notifications')->whereNull('sent_at')->orderBy('id')->limit(200)->get() as $notification) {
            $this->deliver($notification);
        }
    }

    private function sessionActors(BuoiHoc $session): array
    {
        $actors = [];
        $teacher = GiaoVien::find($session->lopHoc->id_giao_vien);
        if ($teacher) {
            $actors[] = $teacher;
        }
        $studentIds = DangKyLop::where('id_lop_hoc', $session->id_lop_hoc)->whereIn('trang_thai', ['da_xac_nhan', 'da_thanh_toan'])->pluck('id_hoc_vien');
        foreach (HocVien::whereIn('id', $studentIds)->get() as $student) {
            $actors[] = $student;
        }

        return $actors;
    }

    private function notify(string $email, string $event, string $body, string $dedupe): void
    {
        $notification = SupportTransaction::run(function () use ($email, $event, $body, $dedupe) {
            $row = DB::table('business_notifications')->where('dedupe_key', $dedupe)->first();
            if ($row) {
                return $row;
            }
            $id = DB::table('business_notifications')->insertGetId(['dedupe_key' => $dedupe, 'recipient' => $email, 'event_type' => $event,
                'body' => $body, 'created_at' => now(), 'updated_at' => now()]);

            return DB::table('business_notifications')->find($id);
        });
        if (! $notification->sent_at) {
            $this->deliver($notification);
        }
    }

    private function deliver(object $notification): void
    {
        $token = Str::uuid()->toString();
        $claimed = SupportTransaction::run(function () use ($notification, $token) {
            return DB::table('business_notifications')->where('id', $notification->id)->whereNull('sent_at')
                ->where(fn ($query) => $query->whereNull('claimed_at')->orWhere('claimed_at', '<', now()->subMinutes(30)))
                ->update(['claimed_at' => now(), 'claim_token' => $token, 'updated_at' => now()]);
        });
        if (! $claimed) {
            return;
        }
        try {
            Mail::to($notification->recipient)->send(new BusinessEventMail($notification->event_type, $notification->body));
            DB::table('business_notifications')->where('id', $notification->id)->where('claim_token', $token)
                ->update(['sent_at' => now(), 'claimed_at' => null, 'claim_token' => null, 'updated_at' => now()]);
        } catch (\Throwable $error) {
            DB::table('business_notifications')->where('id', $notification->id)->where('claim_token', $token)
                ->update(['claimed_at' => null, 'claim_token' => null, 'updated_at' => now()]);
            report($error);
        }
    }
}
