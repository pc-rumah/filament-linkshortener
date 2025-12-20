<?php

namespace App\Services;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceService
{
    public static function generate(Transaction $transaction): string
    {
        $pdf = Pdf::loadView('invoices.invoice', compact('transaction'));

        $path = storage_path(
            'app/invoices/invoice-' . $transaction->order_id . '.pdf'
        );

        // pastikan folder ada
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $pdf->save($path);

        return $path;
    }
}
