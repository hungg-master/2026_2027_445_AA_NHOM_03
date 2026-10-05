<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\DangKyLop;
use App\Models\GiaoVien;
use App\Services\PaymentObligations;
use App\Services\SupportEvents;
use App\Services\SupportTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinanceController extends SupportController
{
    public function obligations(PaymentObligations $service)
    {
        $studentId = Auth::guard('sanctum')->id();
        foreach (DangKyLop::where('id_hoc_vien', $studentId)->get() as $enrollment) {
            $service->sync($enrollment);
        }
        $rows = DB::table('payment_obligations as p')->join('lop_hocs as l', 'l.id', '=', 'p.id_lop_hoc')->join('mon_hocs as m', 'm.id', '=', 'l.id_mon_hoc')
            ->where('p.id_hoc_vien', $studentId)->select('p.*', 'm.ten_mon_hoc')->orderByDesc('p.id')->get();
        foreach ($rows as $row) {
            $row->mode = config('supporting.payments.mode');
            $row->transactions = DB::table('payment_transactions')->where('obligation_id', $row->id)->orderByDesc('id')->get();
        }

        return $this->ok($rows);
    }

    public function requestTest(Request $request, int $id)
    {
        $this->testMode();
        $data = $this->input($request, ['idempotency_key' => 'required|string|min:8|max:100', 'outcome' => 'required|in:success,failed']);
        $created = false;
        $transaction = SupportTransaction::run(function () use ($data, $id, &$created) {
            $obligation = DB::table('payment_obligations')->where('id', $id)->where('id_hoc_vien', Auth::guard('sanctum')->id())->first();
            if (! $obligation) {
                $this->reject('Không tìm thấy khoản học phí.', 404);
            }
            $existing = DB::table('payment_transactions')->where('idempotency_key', $data['idempotency_key'])->first();
            if ($existing) {
                $sameOutcome = $data['outcome'] === 'failed' ? $existing->status === 'failed' : in_array($existing->status, ['awaiting_manual', 'confirmed'], true);
                if ($existing->obligation_id !== $id || ! $sameOutcome) {
                    $this->reject('Mã yêu cầu đã được sử dụng cho giao dịch khác.', 409);
                }

                return $existing;
            }
            if ($obligation->status !== 'pending') {
                $this->reject('Khoản học phí không đang chờ thanh toán.', 409);
            }
            $transactionId = DB::table('payment_transactions')->insertGetId(['obligation_id' => $id,
                'idempotency_key' => $data['idempotency_key'], 'amount' => $obligation->amount, 'mode' => 'test',
                'status' => $data['outcome'] === 'success' ? 'awaiting_manual' : 'failed', 'created_at' => now(), 'updated_at' => now()]);
            $created = true;

            return DB::table('payment_transactions')->find($transactionId);
        });

        return $this->ok($transaction, $transaction->status === 'failed' ? 'Giao dịch thử nghiệm thất bại.' : 'Yêu cầu thử nghiệm đang chờ quản trị viên đối soát.', $created ? 201 : 200);
    }

    public function adminTransactions()
    {
        return $this->ok(DB::table('payment_transactions as t')->join('payment_obligations as p', 'p.id', '=', 't.obligation_id')
            ->join('hoc_viens as h', 'h.id', '=', 'p.id_hoc_vien')->select('t.*', 'p.id_lop_hoc', 'p.id_hoc_vien', 'p.currency', 'h.ho_ten')->orderByDesc('t.id')->limit(200)->get());
    }

    public function settleTest(Request $request, int $id, SupportEvents $events)
    {
        $this->testMode();
        $data = $this->input($request, ['idempotency_key' => 'required|string|min:8|max:100', 'reference' => 'required|string|min:3|max:150']);
        $new = false;
        $result = SupportTransaction::run(function () use ($data, $id, &$new) {
            $transaction = DB::table('payment_transactions')->find($id);
            if (! $transaction) {
                $this->reject('Không tìm thấy giao dịch.', 404);
            }
            if ($transaction->status === 'confirmed') {
                if ($transaction->settlement_key !== $data['idempotency_key'] || $transaction->reference !== $data['reference']) {
                    $this->reject('Giao dịch đã được đối soát bằng mã khác.', 409);
                }

                return $transaction;
            }
            $obligation = DB::table('payment_obligations')->find($transaction->obligation_id);
            $enrollment = DangKyLop::find($obligation->id_dang_ky_lop);
            if ($transaction->mode !== 'test' || $transaction->status !== 'awaiting_manual' || $obligation->status !== 'pending'
                || ! $enrollment || ! in_array($enrollment->trang_thai, ['da_xac_nhan', 'da_thanh_toan'], true)) {
                $this->reject('Giao dịch không đủ điều kiện đối soát.', 409);
            }
            if (DB::table('payment_transactions')->where('settlement_key', $data['idempotency_key'])->orWhere('reference', $data['reference'])->exists()) {
                $this->reject('Mã đối soát đã được sử dụng.', 409);
            }
            DB::table('payment_transactions')->where('id', $id)->update(['status' => 'confirmed', 'settlement_key' => $data['idempotency_key'],
                'reference' => $data['reference'], 'confirmed_by' => Auth::guard('sanctum')->id(), 'confirmed_at' => now(), 'updated_at' => now()]);
            DB::table('payment_obligations')->where('id', $obligation->id)->update(['status' => 'paid', 'updated_at' => now()]);
            $new = true;

            return DB::table('payment_transactions')->find($id);
        });
        if ($new) {
            DB::afterCommit(fn () => $events->paymentConfirmed($id));
        }

        return $this->ok($result, 'Đã đối soát giao dịch thử nghiệm.');
    }

    public function statistics()
    {
        $actor = Auth::guard('sanctum')->user();
        $query = DB::table('payment_transactions as t')->join('payment_obligations as p', 'p.id', '=', 't.obligation_id')->where('t.status', 'confirmed');
        if (! $actor instanceof Admin) {
            $query->where($actor instanceof GiaoVien ? 'p.id_giao_vien' : 'p.id_hoc_vien', $actor->id);
        }

        return $this->ok(['confirmed_amount' => (int) (clone $query)->sum('t.amount'), 'confirmed_count' => (clone $query)->count(), 'currency' => 'VND', 'mode' => config('supporting.payments.mode')]);
    }

    private function testMode(): void
    {
        if (config('supporting.payments.mode') !== 'test') {
            $this->reject('Luồng thanh toán thử nghiệm chưa được bật.', 503);
        }
    }
}
