<?php

namespace Vendor\MoneyFusion\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class MoneyFusionService
{
    protected string $apiUrl;
    protected string $paymentStatusUrl;

    public function __construct()
    {
        $this->apiUrl = config('moneyfusion.api_url');
        $this->paymentStatusUrl = config('moneyfusion.payment_status_url');
    }

    /**
     * Envoie une requête de paiement à MoneyFusion.
     *
     * @param array $paymentData
     * @return array
     * @throws Exception
     */
    public function makePayment(array $paymentData): array
    {
        try {
            $response = Http::post($this->apiUrl, $paymentData);

            $response->throw(); // Lance une exception si le statut HTTP est un code d'erreur (4xx ou 5xx)

            return $response->json();
        } catch (\Illuminate\Http\Client\RequestException $e) {
            // Gérer les erreurs de requête HTTP (ex: 404, 500)
            throw new Exception("Erreur lors de la requête de paiement : " . $e->getMessage(), $e->getCode(), $e);
        } catch (\Throwable $e) {
            // Gérer d'autres erreurs inattendues
            throw new Exception("Une erreur inattendue est survenue : " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Vérifie le statut d'un paiement MoneyFusion.
     *
     * @param string $token
     * @return array
     * @throws Exception
     */
    public function checkPaymentStatus(string $token): array
    {
        try {
            $response = Http::get("{$this->paymentStatusUrl}{$token}");

            $response->throw();

            return $response->json();
        } catch (\Illuminate\Http\Client\RequestException $e) {
            throw new Exception("Erreur lors de la vérification du statut : " . $e->getMessage(), $e->getCode(), $e);
        } catch (\Throwable $e) {
            throw new Exception("Une erreur inattendue est survenue : " . $e->getMessage(), 0, $e);
        }
    }



}