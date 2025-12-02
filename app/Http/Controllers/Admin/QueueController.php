<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class QueueController extends Controller
{
    /**
     * Queue dashboard
     */
    public function index()
    {
        // Pending jobs (bekleyen işler)
        $pendingJobs = DB::table('jobs')
            ->select('queue', DB::raw('count(*) as count'))
            ->groupBy('queue')
            ->get();

        // Failed jobs (başarısız işler)
        $failedJobs = DB::table('failed_jobs')
            ->orderBy('failed_at', 'desc')
            ->paginate(10);

        // İstatistikler
        $stats = [
            'total_pending' => DB::table('jobs')->count(),
            'total_failed' => DB::table('failed_jobs')->count(),
            'emails_pending' => DB::table('jobs')->where('queue', 'emails')->count(),
            'notifications_pending' => DB::table('jobs')->where('queue', 'notifications')->count(),
            'default_pending' => DB::table('jobs')->where('queue', 'default')->count(),
        ];

        // Son 24 saatteki işlemler
        $recentActivity = DB::table('jobs')
            ->select('queue', 'created_at')
            ->where('created_at', '>=', now()->subDay())
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('admin.queue.index', compact('pendingJobs', 'failedJobs', 'stats', 'recentActivity'));
    }

    /**
     * Failed jobs listesi
     */
    public function failed()
    {
        $failedJobs = DB::table('failed_jobs')
            ->orderBy('failed_at', 'desc')
            ->paginate(20);

        return view('admin.queue.failed', compact('failedJobs'));
    }

    /**
     * Failed job detayı
     */
    public function showFailed($id)
    {
        $job = DB::table('failed_jobs')->where('id', $id)->first();

        if (!$job) {
            return redirect()->route('admin.queue.failed')
                ->with('error', 'Job bulunamadı.');
        }

        // Payload'ı decode et
        $payload = json_decode($job->payload, true);
        $exception = $job->exception;

        return view('admin.queue.show-failed', compact('job', 'payload', 'exception'));
    }

    /**
     * Failed job'ı tekrar dene
     */
    public function retryFailed($id)
    {
        try {
            Artisan::call('queue:retry', ['id' => [$id]]);

            return redirect()->route('admin.queue.failed')
                ->with('success', 'Job tekrar denemeye alındı.');
        } catch (\Exception $e) {
            return redirect()->route('admin.queue.failed')
                ->with('error', 'Job tekrar denenemedi: ' . $e->getMessage());
        }
    }

    /**
     * Tüm failed job'ları tekrar dene
     */
    public function retryAllFailed()
    {
        try {
            Artisan::call('queue:retry', ['id' => ['all']]);

            return redirect()->route('admin.queue.failed')
                ->with('success', 'Tüm başarısız job\'lar tekrar denemeye alındı.');
        } catch (\Exception $e) {
            return redirect()->route('admin.queue.failed')
                ->with('error', 'Job\'lar tekrar denenemedi: ' . $e->getMessage());
        }
    }

    /**
     * Failed job'ı sil
     */
    public function deleteFailed($id)
    {
        DB::table('failed_jobs')->where('id', $id)->delete();

        return redirect()->route('admin.queue.failed')
            ->with('success', 'Başarısız job silindi.');
    }

    /**
     * Tüm failed job'ları temizle
     */
    public function flushFailed()
    {
        try {
            Artisan::call('queue:flush');

            return redirect()->route('admin.queue.failed')
                ->with('success', 'Tüm başarısız job\'lar temizlendi.');
        } catch (\Exception $e) {
            return redirect()->route('admin.queue.failed')
                ->with('error', 'Job\'lar temizlenemedi: ' . $e->getMessage());
        }
    }

    /**
     * Queue worker durumunu kontrol et
     */
    public function checkWorker()
    {
        // Son 5 dakikada işlenen job var mı kontrol et
        $recentJobs = DB::table('jobs')
            ->where('created_at', '>=', now()->subMinutes(5))
            ->count();

        $isWorkerRunning = $recentJobs > 0 || Cache::has('queue_worker_heartbeat');

        return response()->json([
            'is_running' => $isWorkerRunning,
            'recent_jobs' => $recentJobs,
            'message' => $isWorkerRunning 
                ? 'Queue worker çalışıyor' 
                : 'Queue worker çalışmıyor! Lütfen "php artisan queue:work" komutunu çalıştırın.',
        ]);
    }

    /**
     * Queue istatistikleri (AJAX)
     */
    public function stats()
    {
        $stats = [
            'total_pending' => DB::table('jobs')->count(),
            'total_failed' => DB::table('failed_jobs')->count(),
            'emails_pending' => DB::table('jobs')->where('queue', 'emails')->count(),
            'notifications_pending' => DB::table('jobs')->where('queue', 'notifications')->count(),
            'default_pending' => DB::table('jobs')->where('queue', 'default')->count(),
            'last_failed' => DB::table('failed_jobs')->orderBy('failed_at', 'desc')->first(),
        ];

        return response()->json($stats);
    }

    /**
     * Test job gönder
     */
    public function testJob(Request $request)
    {
        $type = $request->input('type', 'notification');

        try {
            if ($type === 'notification') {
                \App\Jobs\SendNotificationJob::dispatch(
                    auth()->user(),
                    'test',
                    ['message' => 'Bu bir test bildirimidir.']
                );
            } elseif ($type === 'email') {
                \App\Jobs\SendEmailJob::dispatch(
                    auth()->user(),
                    new \App\Mail\WelcomeEmail(auth()->user())
                );
            } elseif ($type === 'xp') {
                \App\Jobs\ProcessXpEventJob::dispatch(
                    auth()->user(),
                    'test_event',
                    10
                );
            }

            return redirect()->route('admin.queue.index')
                ->with('success', 'Test job kuyruğa eklendi. Queue worker çalışıyorsa birkaç saniye içinde işlenecek.');
        } catch (\Exception $e) {
            return redirect()->route('admin.queue.index')
                ->with('error', 'Test job gönderilemedi: ' . $e->getMessage());
        }
    }
}
