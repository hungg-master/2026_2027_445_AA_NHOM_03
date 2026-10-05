<?php

namespace App\Console\Commands;

use App\Models\BuoiHoc;
use App\Services\SupportEvents;
use Illuminate\Console\Command;

class RemindLessons extends Command
{
    protected $signature = 'supporting:remind {--hours=24 : Số giờ sắp tới cần nhắc lịch}';

    protected $description = 'Gửi nhắc lịch các buổi học đã lên lịch và thử lại thư nghiệp vụ chưa gửi được';

    public function handle(SupportEvents $events): int
    {
        $hours = filter_var($this->option('hours'), FILTER_VALIDATE_INT);
        if ($hours === false || $hours < 1 || $hours > 168) {
            $this->error('hours phải từ 1 đến 168.');

            return self::FAILURE;
        }
        $events->retryPending();
        BuoiHoc::where('trang_thai', 'scheduled')->where('thoi_gian_bat_dau', '>', now())->where('thoi_gian_bat_dau', '<=', now()->addHours($hours))
            ->orderBy('id')->chunkById(100, function ($sessions) use ($events) {
                foreach ($sessions as $session) {
                    $events->remind($session);
                }
            });
        $this->info('Đã xử lý nhắc lịch; thư lỗi được giữ lại để thử sau.');

        return self::SUCCESS;
    }
}
