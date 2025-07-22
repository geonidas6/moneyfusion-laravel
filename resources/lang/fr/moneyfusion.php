<?php

return [
    // Messages de validation
    'validation_total_price_required' => 'Le prix total est obligatoire.',
    'validation_total_price_numeric' => 'Le prix total doit être un nombre.',
    'validation_total_price_min' => 'Le prix total doit être positif.',
    'validation_article_required' => 'Les articles sont obligatoires.',
    'validation_article_array' => 'Les articles doivent être un tableau.',
    'validation_article_min' => 'Vous devez ajouter au moins un article.',
    'validation_numero_send_required' => 'Le numéro de téléphone est obligatoire.',
    'validation_nom_client_required' => 'Le nom du client est obligatoire.',

    // Formulaire de paiement
    'payment_form_title' => 'Paiement avec MoneyFusion',
    'payment_form_error_title' => 'Erreur de paiement!',
    'payment_form_error_generic' => 'Une erreur est survenue lors de la demande de paiement. Veuillez réessayer.',
    'payment_form_total_price' => 'Prix Total',
    'payment_form_articles' => 'Articles (JSON)',
    'payment_form_phone_number' => 'Numéro de Téléphone',
    'payment_form_client_name' => 'Nom du Client',
    'payment_form_personal_info' => 'Infos Personnelles (JSON, optionnel)',
    'payment_form_pay_button' => 'Payer avec MoneyFusion',

    // Callback / Statut
    'callback_missing_token' => 'Jeton de paiement manquant.',
    'callback_status_success_title' => 'Paiement réussi!',
    'callback_status_success_message' => 'Votre paiement a été traité avec succès.',
    'callback_status_pending_title' => 'Paiement en attente!',
    'callback_status_pending_message' => 'Votre paiement est en cours de traitement. Veuillez patienter.',
    'callback_status_failed_title' => 'Paiement échoué!',
    'callback_status_failed_message' => 'Il y a eu un problème avec votre paiement: :message',
    'callback_status_unknown_title' => 'Statut Inconnu',
    'callback_status_unknown_message' => 'Le statut de votre paiement est inconnu. Veuillez vérifier vos transactions ou contacter le support.',
    'transaction_number' => 'Numéro de Transaction',
    'amount' => 'Montant',
    'payment_method' => 'Moyen de Paiement',
    'back_to_home' => 'Retour à l\'accueil',

    // Factures
    'invoices_title' => 'Mes Transactions MoneyFusion',
    'invoices_no_transactions' => 'Vous n\'avez pas encore de transactions.',
    'invoice_id' => 'ID Transaction',
    'invoice_amount' => 'Montant',
    'invoice_status' => 'Statut',
    'invoice_date' => 'Date',
    'invoice_actions' => 'Actions',
    'invoice_view_details' => 'Voir Détails',
    'invoice_download_button' => 'Télécharger Facture',
    'invoice_details_title' => 'Détails de la Transaction #:token',
    'invoice_client' => 'Client',
    'invoice_articles' => 'Articles',
    'invoice_billing_error' => 'Une facture ne peut être générée que pour un paiement réussi.',
    'invoice_number' => 'Numéro de Facture',
    'invoice_date_issued' => 'Date d\'Émission',
    'invoice_payment_status' => 'Statut du Paiement',
    'invoice_mf_transaction_number' => 'Numéro de Transaction MoneyFusion',
    'invoice_description' => 'Description',
    'invoice_unit_price' => 'Prix Unitaire',
    'invoice_quantity' => 'Quantité',
    'invoice_total' => 'Total',
    'invoice_subtotal' => 'Sous-total',
    'invoice_fees' => 'Frais de Transaction',
    'invoice_total_paid' => 'Montant Total Payé',
    'invoice_thank_you' => 'Merci pour votre achat !',
    'invoice_generated_on' => 'Généré le',
    'back_to_transactions' => 'Retour à la liste des transactions',
    'unauthorized_action' => 'Action non autorisée.',

    // Webhook
    'webhook_invalid_payload' => 'Charge utile invalide',
    'webhook_transaction_created' => 'Transaction créée et mise à jour via webhook',
    'webhook_redundant_event' => 'Événement déjà traité ou redondant',
    'webhook_updated_status' => 'Webhook traité avec succès',
    'webhook_no_update_needed' => 'Aucune mise à jour de statut nécessaire',
    'webhook_transaction_not_found' => 'Transaction avec le jeton :token introuvable lors du callback.',


    // ...
    // Notifications par Email
    'email_subject_success' => 'Votre paiement pour la transaction #:id est réussi',
    'email_subject_failure' => 'Paiement échoué pour la transaction #:id',
    'email_heading_success' => 'Confirmation de Paiement',
    'email_heading_failure' => 'Échec de Paiement',
    'email_body_success' => 'Bonjour :name, Votre paiement de :amount a été traité avec succès.',
    'email_body_failure' => 'Bonjour :name, Nous avons le regret de vous informer que votre paiement de :amount pour la transaction #:id a échoué.',
    'email_view_transaction' => 'Voir les détails de la transaction',
    'email_regards' => 'Cordialement',

];