<?php

namespace Sefako\Moneyfusion\Http\Controllers;

use Illuminate\Routing\Controller;
use Sefako\Moneyfusion\Models\MoneyfusionTransaction;
use Illuminate\Http\Request;
use Sefako\Moneyfusion\Facades\Moneyfusion;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = MoneyfusionTransaction::latest()->paginate(10);

        return view('moneyfusion::transactions.index', compact('transactions'));
    }

    public function checkStatus(Request $request, MoneyfusionTransaction $transaction)
    {
        $type = $request->input('type');
        $response = null;

        if ($type === 'payment') {
            $response = Moneyfusion::checkPaymentStatus($transaction->token_pay);
        } elseif ($type === 'payout') {
            $response = Moneyfusion::checkPayoutStatus($transaction->token_pay);
        }

        if ($response && isset($response['statut']) && $response['statut'] === true) {
            $newStatus = $response['status'] ?? $transaction->status; // Assuming API returns 'status' field
            $transaction->status = $newStatus;
            $transaction->save();
            return response()->json(['status' => $newStatus]);
        } else {
            return response()->json(['error' => 'Impossible de vérifier le statut ou statut inchangé.'], 400);
        }
    }
}
