<?php

namespace Sefako\Moneyfusion\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Sefako\Moneyfusion\Facades\Moneyfusion;
use Sefako\Moneyfusion\Models\MoneyfusionTransaction;

class PaymentController extends Controller
{
    public function create()
    {
        return view('moneyfusion::payment.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'totalPrice' => 'required|numeric',
            'numeroSend' => 'required|string',
            'nomclient' => 'required|string',
        ]);

        $paymentData = [
            'totalPrice' => $validatedData['totalPrice'],
            'article' => [], // Add articles if needed
            'personal_Info' => [], // Add personal info if needed
            'numeroSend' => $validatedData['numeroSend'],
            'nomclient' => $validatedData['nomclient'],
            'return_url' => route('moneyfusion.payment.callback'),
            'webhook_url' => route('moneyfusion.webhook'),
        ];

        $response = Moneyfusion::makePayment($paymentData);

        if ($response && $response['statut'] === true) {
            MoneyfusionTransaction::create([
                'user_id' => auth()->id(),
                'token_pay' => $response['token'],
                'type' => 'payment',
                'status' => 'pending',
                'numero_send' => $validatedData['numeroSend'],
                'nom_client' => $validatedData['nomclient'],
                'montant' => $validatedData['totalPrice'],
                'frais' => 0, // Will be updated by webhook
                'moyen' => '', // Will be updated by webhook
            ]);

            return redirect()->away($response['url']);
        }

        return back()->with('error', 'Impossible d\'initier le paiement.');
    }

    public function callback(Request $request)
    {
        // Handle the return from MoneyFusion
        return redirect()->route('moneyfusion.payment.create')->with('success', 'Paiement initié avec succès.');
    }
}
