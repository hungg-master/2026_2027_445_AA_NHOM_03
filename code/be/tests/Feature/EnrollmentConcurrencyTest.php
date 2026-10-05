<?php

namespace Tests\Feature;

use App\Services\FaceVerificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Process\Process;
use Tests\Support\CoreFixtures;
use Tests\TestCase;

class EnrollmentConcurrencyTest extends TestCase
{
    use CoreFixtures;

    private string $concurrencyDirectory;

    private string $concurrencyDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareCoreFixtures();
        $this->concurrencyDirectory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'smarttrial-enrollment-'.bin2hex(random_bytes(12));
        mkdir($this->concurrencyDirectory, 0700);
        $this->concurrencyDatabase = $this->concurrencyDirectory.DIRECTORY_SEPARATOR.'isolated.sqlite';
        touch($this->concurrencyDatabase);
        config([
            'database.default' => 'sqlite', 'database.connections.sqlite.url' => null,
            'database.connections.sqlite.database' => $this->concurrencyDatabase,
        ]);
        DB::purge('sqlite');
        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);
        Mail::fake();
    }

    protected function tearDown(): void
    {
        DB::purge('sqlite');
        parent::tearDown();
        if (isset($this->concurrencyDirectory) && is_dir($this->concurrencyDirectory)) {
            $resolved = realpath($this->concurrencyDirectory);
            $this->assertStringStartsWith(realpath(sys_get_temp_dir()).DIRECTORY_SEPARATOR.'smarttrial-enrollment-', $resolved);
            foreach (glob($this->concurrencyDirectory.DIRECTORY_SEPARATOR.'*') as $path) {
                if (is_file($path)) {
                    unlink($path);
                }
            }
            rmdir($this->concurrencyDirectory);
        }
    }

    private function competingEnrollments(array $requests): array
    {
        $workerPath = $this->concurrencyDirectory.DIRECTORY_SEPARATOR.'worker.php';
        file_put_contents($workerPath, <<<'PHP'
<?php
require $argv[1].'/vendor/autoload.php';
$app = require $argv[1].'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config([
    'app.timezone' => 'Asia/Ho_Chi_Minh',
    'database.default' => 'sqlite', 'database.connections.sqlite.url' => null,
    'database.connections.sqlite.database' => $argv[2],
    'mail.default' => 'array', 'queue.default' => 'sync',
]);
Illuminate\Support\Facades\DB::purge('sqlite');
Illuminate\Support\Facades\Mail::fake();
Carbon\Carbon::setTestNow(Carbon\Carbon::parse('2026-10-04 08:00:00', 'Asia/Ho_Chi_Minh'));
$delayed = false;
Illuminate\Support\Facades\DB::listen(function ($query) use (&$delayed) {
    if (!$delayed && str_starts_with(strtolower($query->sql), 'select') && str_contains($query->sql, '"lop_hocs"')) {
        $delayed = true;
        usleep(250000);
    }
});
$actor = App\Models\HocVien::findOrFail((int) $argv[3]);
file_put_contents($argv[6], 'ready');
$deadline = microtime(true) + 15;
while (!file_exists($argv[7])) {
    if (microtime(true) >= $deadline) throw new RuntimeException('Start barrier timed out');
    usleep(10000);
}
$started = microtime(true);
try {
    $enrollment = app(App\Services\EnrollmentService::class)->register($actor, (int) $argv[4], $argv[5]);
    $result = ['outcome' => 'accepted', 'id' => $enrollment->id, 'status' => $enrollment->trang_thai];
} catch (Illuminate\Validation\ValidationException $e) {
    $result = ['outcome' => 'rejected', 'code' => 422];
} catch (Symfony\Component\HttpKernel\Exception\HttpException $e) {
    $result = ['outcome' => 'rejected', 'code' => $e->getStatusCode()];
} catch (Throwable $e) {
    $result = ['outcome' => 'error', 'type' => get_class($e), 'message' => $e->getMessage()];
}
echo json_encode($result + ['started' => $started, 'finished' => microtime(true)], JSON_THROW_ON_ERROR);
PHP);
        $release = $this->concurrencyDirectory.DIRECTORY_SEPARATOR.'release';
        $processes = [];
        try {
            foreach ($requests as $index => [$student, $class]) {
                $proof = app(FaceVerificationService::class)->issue($student, 'enrollment', $class->id);
                $ready = $this->concurrencyDirectory.DIRECTORY_SEPARATOR.'ready-'.$index;
                $process = new Process([
                    PHP_BINARY, $workerPath, base_path(), $this->concurrencyDatabase,
                    (string) $student->id, (string) $class->id, $proof['verification_id'], $ready, $release,
                ], base_path(), ['APP_ENV' => 'testing']);
                $process->setTimeout(20)->start();
                $processes[] = [$process, $ready];
            }
            $deadline = microtime(true) + 15;
            do {
                $readyCount = count(array_filter($processes, fn ($worker) => file_exists($worker[1])));
                if ($readyCount === count($processes)) {
                    break;
                }
                foreach ($processes as [$process]) {
                    if (! $process->isRunning()) {
                        $this->fail('Enrollment worker exited before barrier: '.$process->getErrorOutput().$process->getOutput());
                    }
                }
                usleep(10000);
            } while (microtime(true) < $deadline);
            $this->assertSame(count($processes), $readyCount, 'Both independent PHP workers must reach the start barrier.');
            touch($release);
            $results = [];
            foreach ($processes as [$process]) {
                $process->wait();
                $this->assertTrue($process->isSuccessful(), $process->getErrorOutput());
                $result = json_decode($process->getOutput(), true, 512, JSON_THROW_ON_ERROR);
                $this->assertNotSame('error', $result['outcome'], $process->getOutput());
                $results[] = $result;
            }
            $this->assertLessThan(min(array_column($results, 'finished')), max(array_column($results, 'started')), 'Requests must overlap in real time.');
            $outcomes = array_column($results, 'outcome');
            sort($outcomes);
            $this->assertSame(['accepted', 'rejected'], $outcomes);
            foreach ($results as $result) {
                if ($result['outcome'] === 'accepted') {
                    $this->assertSame('da_xac_nhan', $result['status']);
                } else {
                    $this->assertContains($result['code'], [403, 409, 422]);
                }
            }

            return $results;
        } finally {
            foreach ($processes as [$process]) {
                if ($process->isRunning()) {
                    $process->stop();
                }
            }
        }
    }

    public function test_two_processes_competing_for_last_place_commit_only_one_enrollment(): void
    {
        $class = $this->coreClass(attributes: ['si_so_toi_da' => 1]);
        $this->coreSession($class);
        $students = [$this->coreStudent(), $this->coreStudent()];
        $this->competingEnrollments([[$students[0], $class], [$students[1], $class]]);
        $this->assertDatabaseCount('dang_ky_lops', 1);
        $this->assertDatabaseCount('payment_obligations', 1);
        $this->assertSame(1, DB::table('face_verifications')->whereNotNull('consumed_at')->count());
    }

    public function test_two_processes_enrolling_same_student_in_overlapping_classes_commit_only_one(): void
    {
        $student = $this->coreStudent();
        $first = $this->coreClass();
        $second = $this->coreClass(attributes: [
            'thoi_gian_bat_dau' => '2026-10-11 09:30:00', 'thoi_gian_ket_thuc' => '2026-10-11 10:30:00',
        ]);
        $this->coreSession($first);
        $this->coreSession($second);
        $this->competingEnrollments([[$student, $first], [$student, $second]]);
        $this->assertDatabaseCount('dang_ky_lops', 1);
        $this->assertDatabaseHas('dang_ky_lops', ['id_hoc_vien' => $student->id, 'trang_thai' => 'da_xac_nhan']);
        $this->assertDatabaseCount('payment_obligations', 1);
        $this->assertSame(1, DB::table('face_verifications')->whereNotNull('consumed_at')->count());
    }
}
