<?php

namespace App\Http\Controllers;

use App\Models\Plans;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CheckoutController extends Controller
{
    public function index()
    {
        $basic = Plans::where('slug', 'basic')->first();
        $pro = Plans::where('slug', 'pro')->first();

        return view('pricing', compact('basic', 'pro'));
    }

    public function pay(Request $request)
    {
        $plan = Plans::where('slug', $request->plan)->firstOrFail();

        $payload = [
            'transaction_details' => [
                'order_id' => 'PLAN-' . uniqid(),
                'gross_amount' => $plan->price,
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ],
        ];

        $snapToken = \Midtrans\Snap::getSnapToken($payload);

        return response()->json(['token' => $snapToken]);
    }

    public function callback(Request $request)
    {
        $data = $request->all();

        $orderId = $data['order_id'];
        $status  = $data['transaction_status'];
        $fraud   = $data['fraud_status'] ?? null;

        $transaction = Transaction::where('order_id', $orderId)->first();

        if (!$transaction) return;

        // Verifikasi signature
        $expectedSignature = hash(
            'sha512',
            $data['order_id'] .
                $data['status_code'] .
                $data['gross_amount'] .
                config('midtrans.server_key')
        );

        if ($expectedSignature !== $data['signature_key']) {
            return response()->json(['message' => 'invalid signature'], 403);
        }

        if ($status == 'capture' || $status == 'settlement') {

            if ($fraud == 'challenge') {
                $transaction->update(['status' => 'challenge']);
            } else {
                $transaction->update(['status' => 'paid']);

                $transaction->user->update([
                    'plan' => 'pro',
                    'plan_expired_at' => now()->addMonth()
                ]);

                // SIMPAN FLAG NOTIF (5 menit cukup)
                Cache::put(
                    'payment_success_user_' . $transaction->user_id,
                    true,
                    now()->addMinutes(5)
                );
            }
        } elseif ($status == 'pending') {
            $transaction->update(['status' => 'pending']);
        } elseif (in_array($status, ['deny', 'cancel', 'expire'])) {
            $transaction->update(['status' => 'failed']);
        }

        return response()->json(['message' => 'OK']);
    }
}
