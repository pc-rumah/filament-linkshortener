<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Mail\InvoicePaidMail;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessInvoiceEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Transaction $transaction)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $invoicePath = InvoiceService::generate($this->transaction);

        Mail::to($this->transaction->user->email)->send(new InvoicePaidMail($this->transaction, $invoicePath));
    }
}
