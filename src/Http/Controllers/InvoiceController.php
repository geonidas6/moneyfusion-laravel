<?php

namespace Vendor\MoneyFusion\Http\Controllers;

use App\Http\Controllers\Controller;
use Vendor\MoneyFusion\Models\MoneyFusionTransaction;
use Illuminate\Http\Request;
use PDF; // Importez la façade PDF (pour dompdf)

class InvoiceController extends Controller
{
    /**
     * Affiche la liste des transactions pour l'utilisateur authentifié.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $transactions = MoneyFusionTransaction::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(10); // Pagination pour les grandes listes

        return view('moneyfusion::invoices.index', compact('transactions'));
    }

    /**
     * Affiche les détails d'une transaction spécifique pour l'utilisateur.
     *
     * @param MoneyFusionTransaction $transaction
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(MoneyFusionTransaction $transaction)
    {
        // Vérifiez que l'utilisateur est bien le propriétaire de la transaction
        if ($transaction->user_id !== auth()->id()) {
            abort(403, __('moneyfusion.unauthorized_action'));
        }

        return view('moneyfusion::invoices.show', compact('transaction'));
    }

    /**
     * Génère et télécharge la facture PDF pour une transaction.
     *
     * @param MoneyFusionTransaction $transaction
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function download(MoneyFusionTransaction $transaction)
    {
        // Vérifiez que l'utilisateur est bien le propriétaire de la transaction
        if ($transaction->user_id !== auth()->id()) {
            abort(403, __('moneyfusion.unauthorized_action'));
        }

        // Assurez-vous que la transaction est "paid" pour générer une facture
        if ($transaction->status !== 'paid') {
            return back()->withErrors(['invoice_error' => __('moneyfusion.invoice_billing_error')]);
        }

        // Chargez les données nécessaires pour la facture
        $invoiceData = [
            'transaction' => $transaction,
            'company' => config('moneyfusion.billing'),
            'invoice_number' => config('moneyfusion.billing.invoice_prefix') . str_pad($transaction->id, 8, '0', STR_PAD_LEFT), // Numéro de facture unique
            'invoice_date' => now()->format('d/m/Y'),
        ];

        // Charger la vue de la facture dans le PDF
        $pdf = PDF::loadView('moneyfusion::invoices.pdf', $invoiceData);

        // Télécharger le PDF
        return $pdf->download("facture-{$invoiceData['invoice_number']}.pdf");
    }
}