<?php

namespace Vendor\MoneyFusion\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Vendor\MoneyFusion\Services\MoneyFusionService;
use Vendor\MoneyFusion\Models\MoneyFusionTransaction;
use Vendor\MoneyFusion\Http\Requests\PaymentRequest;
use Illuminate\Support\Facades\Log;

class MoneyFusionController extends Controller
{
    protected MoneyFusionService $moneyFusionService;

    public function __construct(MoneyFusionService $moneyFusionService)
    {
        $this->moneyFusionService = $moneyFusionService;
    }

    /**
     * Affiche le formulaire de paiement.
     * @return \Illuminate\Contracts\View\View
     */
    public function showPaymentForm()
    {
        return view('moneyfusion::payment-form');
    }

    /**
     * Traite la soumission du formulaire de paiement.
     *
     * @param PaymentRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processPayment(PaymentRequest $request)
    {
        try {
            $paymentData = $request->validated();

            $paymentData['return_url'] = route('moneyfusion.callback');
            $paymentData['webhook_url'] = route('moneyfusion.webhook');

            $response = $this->moneyFusionService->makePayment($paymentData);

            if ($response['statut']) {
                MoneyFusionTransaction::create([
                    'user_id' => auth()->id(), // Ajout de l'ID de l'utilisateur authentifié
                    'token_pay' => $response['token'],
                    'numero_send' => $paymentData['numeroSend'],
                    'nom_client' => $paymentData['nomclient'],
                    'articles' => $paymentData['article'],
                    'total_price' => $paymentData['totalPrice'],
                    'personal_info' => $paymentData['personal_Info'] ?? null,
                    'return_url' => $paymentData['return_url'],
                    'webhook_url' => $paymentData['webhook_url'],
                    'status' => 'pending',
                ]);

                return redirect()->away($response['url']);
            } else {
                return back()->withErrors(['moneyfusion_error' => $response['message'] ?? __('moneyfusion.payment_form_error_generic')]);
            }
        } catch (\Exception $e) {
            Log::error("MoneyFusion Payment Error: " . $e->getMessage(), ['exception' => $e]);
            return back()->withErrors(['moneyfusion_error' => __('moneyfusion.payment_form_error_generic')]);
        }
    }

    /**
     * Gère le retour de MoneyFusion après paiement.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function handleCallback(Request $request)
    {
        $token = $request->query('token'); // Le token pourrait être passé en paramètre URL ou de session

        if (!$token) {
            return view('moneyfusion::callback', ['status' => 'error', 'message' => __('moneyfusion.callback_missing_token')]);
        }else {
            return view('moneyfusion::callback', ['status' => 'error', 'message' => $statusResponse['message'] ?? __('moneyfusion.callback_status_failed_message', ['message' => ''])]);
        }

        try {
            $statusResponse = $this->moneyFusionService->checkPaymentStatus($token);

            if ($statusResponse['statut']) {
                $transactionData = $statusResponse['data'];

                $transaction = MoneyFusionTransaction::where('token_pay', $transactionData['tokenPay'])->first();

                if ($transaction) {
                    $oldStatus = $transaction->status; // Sauvegardez l'ancien statut
                    $transaction->update([
                        'status' => $transactionData['statut'],
                        'transaction_number' => $transactionData['numeroTransaction'] ?? null,
                        'fees' => $transactionData['frais'] ?? null,
                        'payment_method' => $transactionData['moyen'] ?? null,
                    ]);

                    // Envoyer l'e-mail si le statut a changé et est final
                    if ($oldStatus !== $transaction->status) {
                        if ($transaction->status === 'paid') {
                            $this->sendPaymentNotification($transaction, 'success');
                        } elseif (in_array($transaction->status, ['failure', 'no paid', 'cancelled'])) {
                            $this->sendPaymentNotification($transaction, 'failure');
                        }
                    }
                } else {
                    // Gérer le cas où la transaction n'est pas trouvée (ex: si le webhook n'a pas encore créé l'entrée)
                    Log::warning("Transaction with token {$token} not found during callback. Status: " . $transactionData['statut']);
                }

                return view('moneyfusion::callback', [
                    'status' => $transactionData['statut'],
                    'transaction' => $transactionData,
                ]);
            } else {
                return view('moneyfusion::callback', ['status' => 'error', 'message' => $statusResponse['message'] ?? 'Échec de la vérification du statut.']);
            }
        } catch (\Exception $e) {
            Log::error("MoneyFusion Callback Error for token {$token}: " . $e->getMessage(), ['exception' => $e]);
            return view('moneyfusion::callback', ['status' => 'error', 'message' => __('moneyfusion.callback_status_failed_message', ['message' => 'Une erreur est survenue lors de la vérification du statut.'])]);
        }
    }

    /**
     * Gère les notifications de webhook de MoneyFusion.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleWebhook(Request $request)
    {
        try {
            $payload = $request->all();
            $tokenPay = $payload['tokenPay'] ?? null;
            $event = $payload['event'] ?? null;

            if (!$tokenPay || !$event) {
                Log::warning("MoneyFusion Webhook: " . __('moneyfusion.webhook_invalid_payload'), ['payload' => $payload]);
                return response()->json(['message' => __('moneyfusion.webhook_invalid_payload')], 400); // Bad Request
            }

            Log::info("MoneyFusion Webhook received: Event '{$event}' for token '{$tokenPay}'", ['payload' => $payload]);

            $transaction = MoneyFusionTransaction::where('token_pay', $tokenPay)->first();

            if (!$transaction) {
                Log::info("MoneyFusion Webhook: Creating new transaction for token {$tokenPay}.");
                $transaction = MoneyFusionTransaction::create([
                    'user_id' => $payload['personal_Info'][0]['userId'] ?? null, // Important: assurez-vous que userId est passé dans personal_Info
                    'token_pay' => $tokenPay,
                    'numero_send' => $payload['numeroSend'] ?? 'N/A',
                    'nom_client' => $payload['nomclient'] ?? 'N/A',
                    'articles' => [['unknown' => $payload['Montant'] ?? 0]],
                    'total_price' => $payload['Montant'] ?? 0,
                    'personal_info' => $payload['personal_Info'] ?? null,
                    'return_url' => $payload['return_url'] ?? null,
                    'webhook_url' => $payload['webhook_url'] ?? null,
                    'status' => $this->mapWebhookStatus($event),
                    'transaction_number' => $payload['numeroTransaction'] ?? null,
                    'fees' => $payload['frais'] ?? null,
                ]);

                // Envoyer l'e-mail si le statut est final dès la création
                if ($transaction->status === 'paid') {
                    $this->sendPaymentNotification($transaction, 'success');
                } elseif (in_array($transaction->status, ['failure', 'no paid', 'cancelled'])) {
                    $this->sendPaymentNotification($transaction, 'failure');
                }

                return response()->json(['message' => __('moneyfusion.webhook_transaction_created')], 200);
            }

            $currentStatus = $transaction->status;
            $incomingStatus = $this->mapWebhookStatus($event);

            if ($currentStatus === $incomingStatus) {
                Log::info("MoneyFusion Webhook: Ignoring redundant event '{$event}' for token '{$tokenPay}'. Current status is already '{$currentStatus}'.");
                return response()->json(['message' => __('moneyfusion.webhook_redundant_event')], 200);
            }

            if ($this->shouldUpdateStatus($currentStatus, $incomingStatus)) {
                $transaction->update([
                    'status' => $incomingStatus,
                    'transaction_number' => $payload['numeroTransaction'] ?? $transaction->transaction_number,
                    'fees' => $payload['frais'] ?? $transaction->fees,
                ]);
                Log::info("MoneyFusion Webhook: Transaction '{$tokenPay}' updated to status '{$incomingStatus}'.");

                // Envoyer l'e-mail si le statut a changé et est final
                if ($transaction->status === 'paid') {
                    $this->sendPaymentNotification($transaction, 'success');
                } elseif (in_array($transaction->status, ['failure', 'no paid', 'cancelled'])) {
                    $this->sendPaymentNotification($transaction, 'failure');
                }

                return response()->json(['message' => __('moneyfusion.webhook_updated_status')], 200);
            }

            Log::info("MoneyFusion Webhook: Not updating status for token '{$tokenPay}'. Current '{$currentStatus}', incoming '{$incomingStatus}'.");
            return response()->json(['message' => __('moneyfusion.webhook_no_update_needed')], 200);

        } catch (\Throwable $e) {
            // Capture toutes les exceptions non gérées et log-les
            Log::error("MoneyFusion Webhook processing error: " . $e->getMessage(), [
                'exception' => $e,
                'payload' => $request->all(),
            ]);
            // Retourne un statut 500 pour indiquer à MoneyFusion de réessayer
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Mappe l'événement du webhook au statut de la base de données.
     *
     * @param string $event
     * @return string
     */
    protected function mapWebhookStatus(string $event): string
    {
        return match ($event) {
            'payin.session.pending' => 'pending',
            'payin.session.completed' => 'paid',
            'payin.session.cancelled' => 'cancelled', // On peut aussi le mapper à 'failure' ou 'no paid' si on veut
            default => 'unknown',
        };
    }

    /**
     * Détermine si le statut de la transaction doit être mis à jour.
     *
     * @param string $currentStatus
     * @param string $incomingStatus
     * @return bool
     */
    protected function shouldUpdateStatus(string $currentStatus, string $incomingStatus): bool
    {
        // Logique pour éviter les régressions de statut ou les mises à jour inutiles
        if ($incomingStatus === 'pending' && in_array($currentStatus, ['paid', 'cancelled', 'failure'])) {
            return false; // Ne pas repasser à pending si déjà payé ou annulé
        }
        if ($incomingStatus === 'cancelled' && $currentStatus === 'paid') {
            return false; // Un paiement réussi ne doit pas être annulé par un webhook
        }
        // Ajouter d'autres règles si nécessaire (ex: "failure" > "paid" n'est pas possible)
        return true;
    }

    /**
     * Sends a payment status notification email.
     *
     * @param MoneyFusionTransaction $transaction
     * @param string $statusType 'success' or 'failure'
     * @return void
     */
    protected function sendPaymentNotification(MoneyFusionTransaction $transaction, string $statusType): void
    {
        // Vérifiez que l'utilisateur existe et qu'il a une adresse e-mail
        if ($transaction->user && $transaction->user->email) {
            try {
                Mail::to($transaction->user->email)->send(new PaymentStatusNotification($transaction, $statusType));
                Log::info("MoneyFusion: Payment status email sent to {$transaction->user->email} for transaction {$transaction->token_pay} ({$statusType}).");
            } catch (\Throwable $e) {
                Log::error("MoneyFusion: Failed to send payment status email to {$transaction->user->email} for transaction {$transaction->token_pay}: " . $e->getMessage(), ['exception' => $e]);
            }
        } else {
            Log::warning("MoneyFusion: Cannot send payment status email for transaction {$transaction->token_pay}. User or email missing.");
        }
    }

}