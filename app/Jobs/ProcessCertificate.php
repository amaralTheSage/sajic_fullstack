<?php

namespace App\Jobs;

use App\Mail\ActuallySendCertificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProcessCertificate implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $user) { }
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $file_name = $this->user['id'] . '2025_' . Str::replace(' ', '_', $this->user['name']) . '.pdf';

        if (Storage::disk('public')->exists('certificates/' . $file_name)){
            return;
        }

        $certificate = Pdf::loadView('pdf.certificate', [
            'file_name' => $file_name,
            'name' => Str::upper($this->user['name']),
        ])
            ->setPaper('a4', 'landscape')
            ->output();

        Storage::disk('public')->put('certificates/' . $file_name, $certificate);

        Mail::to($this->user['email'])->queue(new ActuallySendCertificate($file_name));
    }
}
