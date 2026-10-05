<?php

namespace App\Http\Controllers;

use App\Models\GiaoVien;
use App\Models\HocVien;
use App\Services\ConsultationAccess;
use App\Services\SupportTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConsultationController extends SupportController
{
    public function contacts()
    {
        $query = ConsultationAccess::role() === 'hoc_vien'
            ? GiaoVien::where('trang_thai_duyet', 'da_duyet') : HocVien::query();

        return $this->ok($query->where('is_active', 1)->where('is_block', 0)->where('tinh_trang', 1)->select('id', 'ho_ten')->orderBy('ho_ten')->limit(200)->get());
    }

    public function index()
    {
        $conversations = ConsultationAccess::owned()->orderByDesc('updated_at')->limit(100)->get();
        foreach ($conversations as $conversation) {
            $isStudent = ConsultationAccess::role() === 'hoc_vien';
            $model = $isStudent ? GiaoVien::class : HocVien::class;
            $conversation->counterparty = $model::where('id', $isStudent ? $conversation->id_giao_vien : $conversation->id_hoc_vien)->first(['id', 'ho_ten']);
            $conversation->counterparty_role = $isStudent ? 'giao_vien' : 'hoc_vien';
        }

        return $this->ok($conversations);
    }

    public function store(Request $request)
    {
        $student = ConsultationAccess::role() === 'hoc_vien';
        $key = $student ? 'id_giao_vien' : 'id_hoc_vien';
        $data = $this->input($request, [$key => 'required|integer|min:1']);
        $model = $student ? GiaoVien::class : HocVien::class;
        $query = $model::where('id', $data[$key])->where('is_active', 1)->where('is_block', 0)->where('tinh_trang', 1);
        if ($student) {
            $query->where('trang_thai_duyet', 'da_duyet');
        }
        if (! $query->exists()) {
            $this->reject('Không tìm thấy người tư vấn đang hoạt động.', 404);
        }
        $conversation = SupportTransaction::run(function () use ($student, $data, $key) {
            $pair = ['id_hoc_vien' => $student ? Auth::guard('sanctum')->id() : $data[$key],
                'id_giao_vien' => $student ? $data[$key] : Auth::guard('sanctum')->id()];
            $existing = DB::table('consultation_conversations')->where($pair)->first();
            if ($existing) {
                return $existing;
            }
            $id = DB::table('consultation_conversations')->insertGetId($pair + ['created_at' => now(), 'updated_at' => now()]);

            return DB::table('consultation_conversations')->find($id);
        });

        return $this->ok($conversation, 'Đã mở hội thoại.');
    }

    public function messages(Request $request, int $id)
    {
        $data = $this->input($request, ['after_id' => 'sometimes|integer|min:0']);
        $this->authorizeConversation($id);

        return $this->ok(DB::table('consultation_messages')->where('conversation_id', $id)->where('id', '>', $data['after_id'] ?? 0)->orderBy('id')->limit(100)->get());
    }

    public function send(Request $request, int $id)
    {
        $data = $this->input($request, ['body' => 'required|string|max:4000']);
        if (trim($data['body']) === '') {
            $this->reject('Tin nhắn không được để trống.', 422);
        }
        $message = SupportTransaction::run(function () use ($data, $id) {
            $this->authorizeConversation($id);
            $messageId = DB::table('consultation_messages')->insertGetId(['conversation_id' => $id,
                'sender_role' => ConsultationAccess::role(), 'sender_id' => Auth::guard('sanctum')->id(),
                'body' => trim($data['body']), 'created_at' => now(), 'updated_at' => now()]);
            DB::table('consultation_conversations')->where('id', $id)->update(['updated_at' => now()]);

            return DB::table('consultation_messages')->find($messageId);
        });

        return $this->ok($message, 'Đã gửi tin nhắn.', 201);
    }

    private function authorizeConversation(int $id): void
    {
        if (! ConsultationAccess::owned()->where('id', $id)->exists()) {
            $this->reject('Không tìm thấy hội thoại.', 404);
        }
    }
}
