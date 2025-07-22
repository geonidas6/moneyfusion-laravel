<?php

namespace Vendor\MoneyFusion\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ou false si vous avez une logique d'autorisation spécifique
    }

    public function rules(): array
    {
        return [
            'totalPrice' => ['required', 'numeric', 'min:0'],
            'article' => ['required', 'array', 'min:1'],
            'article.*' => ['required', 'array'], // Chaque article est un objet (array)
            'numeroSend' => ['required', 'string', 'max:20'], // Adapter la taille si besoin
            'nomclient' => ['required', 'string', 'max:255'],
            'personal_Info' => ['nullable', 'array'],
            'personal_Info.*' => ['nullable', 'array'], // Chaque personal_Info est un objet (array)
            // return_url et webhook_url seront définis par le package par défaut,
            // mais peuvent être passés par l'utilisateur s'il veut les personnaliser.
            // 'return_url' => ['nullable', 'url'],
            // 'webhook_url' => ['nullable', 'url'],
        ];
    }

    public function messages(): array
    {
        return [
            'totalPrice.required' => __('moneyfusion.validation_total_price_required'),
            'totalPrice.numeric' => __('moneyfusion.validation_total_price_numeric'),
            'totalPrice.min' => __('moneyfusion.validation_total_price_min'),
            'article.required' => __('moneyfusion.validation_article_required'),
            'article.array' => __('moneyfusion.validation_article_array'),
            'article.min' => __('moneyfusion.validation_article_min'),
            'numeroSend.required' => __('moneyfusion.validation_numero_send_required'),
            'nomclient.required' => __('moneyfusion.validation_nom_client_required'),
        ];
    }
}