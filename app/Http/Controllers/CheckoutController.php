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
        $user = auth()->user();

        return view('pricing', compact('basic', 'pro', 'user'));
    }

    public function pay(Request $request)
    {
        \Log::info('PAYMENT REQUEST', [
            'user_id' => auth()->id(),
            'plan' => $request->plan
        ]);

        $plan = Plans::where('slug', $request->plan)->firstOrFail();

        $orderId = 'PLAN-' . uniqid();

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $plan->price,
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ],
        ];

        \Log::info('MIDTRANS PAYLOAD', $payload);

        // SIMPAN TRANSAKSI SEBELUM REQUEST SNAP TOKEN
        Transaction::create([
            'user_id' => auth()->id(),
            'order_id' => $orderId,
            'plan' => $plan->slug,
            'amount' => $plan->price,
            'status' => 'pending',
        ]);

        $snapToken = \Midtrans\Snap::getSnapToken($payload);

        \Log::info('SNAP TOKEN', ['token' => $snapToken]);

        return response()->json(['token' => $snapToken]);
    }

    public function callback(Request $request)
    {
        \Log::info('MIDTRANS CALLBACK RECEIVED', $request->all());

        $data = $request->all();

        $orderId = $data['order_id'];
        $status  = $data['transaction_status'];
        $fraud   = $data['fraud_status'] ?? null;

        $transaction = Transaction::where('order_id', $orderId)->first();

        if (!$transaction) {
            \Log::warning('TRANSACTION NOT FOUND', ['order_id' => $orderId]);
            return;
        }

        // Verifikasi signature
        $expectedSignature = hash(
            'sha512',
            $data['order_id'] .
                $data['status_code'] .
                $data['gross_amount'] .
                config('midtrans.server_key')
        );

        if ($expectedSignature !== $data['signature_key']) {
            \Log::error('INVALID SIGNATURE', [
                'expected' => $expectedSignature,
                'received' => $data['signature_key']
            ]);
            return response()->json(['message' => 'invalid signature'], 403);
        }

        \Log::info('TRANSACTION STATUS', [
            'order_id' => $orderId,
            'status' => $status,
            'fraud' => $fraud
        ]);

        if ($status == 'capture' || $status == 'settlement') {

            if ($fraud == 'challenge') {
                $transaction->update(['status' => 'challenge']);
                \Log::info('TRANSACTION CHALLENGE', ['order_id' => $orderId]);
            } else {
                $transaction->update(['status' => 'paid']);
                $transaction->user->update([
                    'plan_id' => 2,
                    'plan_expired_at' => now()->addMonth()
                ]);
                \Log::info('TRANSACTION PAID & USER UPGRADED', [
                    'user_id' => $transaction->user_id
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
            \Log::info('TRANSACTION PENDING', ['order_id' => $orderId]);
        } elseif (in_array($status, ['deny', 'cancel', 'expire'])) {
            $transaction->update(['status' => 'failed']);
            \Log::info('TRANSACTION FAILED', ['order_id' => $orderId, 'status' => $status]);
        }

        return response()->json(['message' => 'OK']);
    }
}
