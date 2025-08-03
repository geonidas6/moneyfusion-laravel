<?php

namespace Sefako\Moneyfusion\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Sefako\Moneyfusion\Facades\Moneyfusion;
use Sefako\Moneyfusion\Models\MoneyfusionTransaction;

class PayoutController extends Controller
{
    public function create()
    {
        return view('moneyfusion::payout.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'montant' => 'required|numeric',
            'numero' => 'required|string',
            'moyen' => 'required|string',
        ]);

        $payoutData = [
            'montant' => $validatedData['montant'],
            'numero' => $validatedData['numero'],
            'moyen' => $validatedData['moyen'],
            'webhook_url' => route('moneyfusion.webhook'),
        ];

        $response = Moneyfusion::requestPayout($payoutData);

        if ($response && $response['statut'] === true) {
            MoneyfusionTransaction::create([
                'user_id' => auth()->id(),
                'token_pay' => $response['token'],
                'type' => 'payout',
                'status' => 'pending',
                'numero_send' => $validatedData['numero'],
                'nom_client' => '', // Not available in payout
                'montant' => $validatedData['montant'],
                'frais' => 0, // Will be updated by webhook
                'moyen' => $validatedData['moyen'],
            ]);

            return back()->with('success', 'Demande de retrait initiée avec succès.');
        }

        return back()->with('error', 'Impossible d\'initier le retrait.');
    }
}
