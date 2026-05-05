<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function createStripeIntent(Request $request)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $user = $request->user();

            $paymentIntent = PaymentIntent::create([
                'amount' => 10000,
                'currency' => 'usd',
                'metadata' => [
                    'job_id' => $request->job_id,
                    'user_id' => $user->id
                ]
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
