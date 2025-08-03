<?php

namespace Sefako\Moneyfusion;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Moneyfusion
{
    protected $apiUrl;
    protected $apiKey;
    protected $makePaymentApiUrl;

    public function __construct()
    {
        $this->apiUrl = config('moneyfusion.api_url');
        $this->apiKey = config('moneyfusion.api_key');
        $this->makePaymentApiUrl = config('moneyfusion.make_payment_api_url');
    }

    public function makePayment(array $paymentData)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->post($this->makePaymentApiUrl, $paymentData);

        Log::info('MoneyFusion API Response: ' . $response->body());

        return $response->json();
    }

    public function checkPaymentStatus(string $token)
    {
        $response = Http::get($this->apiUrl . '/paiementNotif/' . $token);

        Log::info('MoneyFusion API Response: ' . $response->body());

        return $response->json();
    }

    public function handleWebhook($request)
    {
        // Verify the webhook signature (if any)

        $payload = $request->all();

        Log::info('MoneyFusion Webhook Received: ' . json_encode($payload));

        // Process the webhook event
        switch ($payload['event']) {
            case 'payin.session.completed':
                // Handle successful payment
                break;
            case 'payin.session.cancelled':
                // Handle failed payment
                break;
            case 'payout.session.completed':
                // Handle successful payout
                break;
            case 'payout.session.cancelled':
                // Handle failed payout
                break;
            // Add other event cases as needed
        }

        return response()->json(['status' => 'success']);
    }

    public function requestPayout(array $payoutData)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->post($this->apiUrl . '/payout', $payoutData);

        Log::info('MoneyFusion Payout API Response: ' . $response->body());

        return $response->json();
    }

    public function checkPayoutStatus(string $token)
    {
        $response = Http::get($this->apiUrl . '/payoutNotif/' . $token);

        Log::info('MoneyFusion Payout API Response: ' . $response->body());

        return $response->json();
    }
}
