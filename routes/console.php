<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\FindMatchesJob;
use App\Jobs\CleanupExpiredQueuesJob;
use App\Jobs\CleanupExpiredMatchesJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Matchmaking Scheduled Jobs
 * 
 * Requirements: 1.2, 1.4, 4.4
 */

// Her 10 saniyede bir eşleşme ara
Schedule::job(new FindMatchesJob())->everyTenSeconds();

// Her dakika süresi dolmuş kuyrukları temizle
Schedule::job(new CleanupExpiredQueuesJob())->everyMinute();

// Her dakika süresi dolmuş eşleşmeleri temizle
Schedule::job(new CleanupExpiredMatchesJob())->everyMinute();
