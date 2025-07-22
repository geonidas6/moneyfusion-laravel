php artisan vendor:publish --provider="Vendor\MoneyFusion\MoneyFusionServiceProvider" --tag="moneyfusion-config"
php artisan vendor:publish --provider="Vendor\MoneyFusion\MoneyFusionServiceProvider" --tag="moneyfusion-migrations"
php artisan vendor:publish --provider="Vendor\MoneyFusion\MoneyFusionServiceProvider" --tag="moneyfusion-views"


php artisan migrate


MONEYFUSION_API_URL="VOTRE_URL_API_DE_MONEYFUSION"
# MONEYFUSION_PAYMENT_STATUS_URL est par défaut, mais peut être surchargée si l'API change.
# MONEYFUSION_WEBHOOK_SECRET="VOTRE_SECRET_WEBHOOK_SI_EXISTANT"



Utilisation :

Formulaire de paiement : Accéder à your-app.com/moneyfusion (ou le préfixe configuré).

Paiement direct (depuis votre code) :

PHP

use Vendor\MoneyFusion\Facades\MoneyFusion;
use Exception;

try {
$paymentData = [
'totalPrice' => 200,
'article' => [['sac' => 100, 'chaussure' => 100]],
'personal_Info' => [['userId' => 1, 'orderId' => 123]],
'numeroSend' => '01010101',
'nomclient' => 'John Doe',
// return_url et webhook_url peuvent être omis ici, le package les ajoutera par défaut
];
$response = MoneyFusion::makePayment($paymentData);

    if ($response['statut']) {
        // Rediriger l'utilisateur vers $response['url']
        return redirect()->away($response['url']);
    }
} catch (Exception $e) {
// Gérer l'erreur
}
Vérifier le statut manuellement :

PHP

use Vendor\MoneyFusion\Facades\MoneyFusion;
use Exception;

$token = 'votre_token_de_transaction';
try {
$status = MoneyFusion::checkPaymentStatus($token);
// $status contient les détails de la transaction
} catch (Exception $e) {
// Gérer l'erreur
}


Planification de la Commande
Dans le fichier App\Console\Kernel de l'application hôte, vous planifierez cette commande. Les utilisateurs de votre package devront ajouter ceci.

app/Console/Kernel.php (dans l'application utilisateur)

PHP

protected function schedule(Schedule $schedule): void
{
// ...
$schedule->command('moneyfusion:check-pending')->everyFifteenMinutes(); // Ou une fréquence adaptée
}