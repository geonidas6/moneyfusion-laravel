<?php

namespace Vendor\MoneyFusion\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyMoneyFusionWebhookSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $signature = $request->header('X-MoneyFusion-Signature'); // Nom d'en-tête hypothétique
        $secret = config('moneyfusion.webhook_secret');

        if (!$signature || !$secret) {
            Log::warning('MoneyFusion Webhook: Signature ou secret manquant.');
            return response()->json(['message' => 'Unauthorized: Signature or secret missing'], 403);
        }

        // Ici, vous implémenteriez la logique de vérification de la signature
        // par exemple: hash_hmac('sha256', $request->getContent(), $secret)
        // et comparer avec $signature.
        // Si la signature n'est pas valide:
        // Log::warning('MoneyFusion Webhook: Signature invalide.');
        // return response()->json(['message' => 'Unauthorized: Invalid signature'], 403);

        Log::info('MoneyFusion Webhook: Signature validée (si implémentée).'); // À supprimer une fois la vraie logique implémentée

        return $next($request);
    }
}