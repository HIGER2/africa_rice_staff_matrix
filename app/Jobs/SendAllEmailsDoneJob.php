<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class SendAllEmailsDoneJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Vérifier si tous les emails sont envoyés
        if (Cache::get('emails_sent') >= Cache::get('emails_total')) {
            // Envoyer la notification à l'admin
            Mail::raw('All emails have been successfully sent.', function ($message) {
                $message->to('admin@example.com')
                        ->subject('Queue Finished Notification');
            });

            // Supprimer le verrou et les compteurs
            Cache::forget('emails_total');
            Cache::forget('emails_sent');
            Cache::forget('mail_queue_running');
        }
    }
}
