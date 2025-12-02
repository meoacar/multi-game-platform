<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Job çalıştırma denemeleri
     */
    public $tries = 5;

    /**
     * Job timeout süresi (saniye)
     */
    public $timeout = 60;

    /**
     * Yeniden deneme aralıkları (saniye)
     */
    public $backoff = [60, 300, 900]; // 1dk, 5dk, 15dk

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public Mailable $mailable
    ) {
        $this->onQueue('emails');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->user->email)->send($this->mailable);
    }

    /**
     * Job başarısız olduğunda
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Email gönderimi başarısız: ' . $exception->getMessage(), [
            'user_id' => $this->user->id,
            'email' => $this->user->email,
        ]);
    }
}
