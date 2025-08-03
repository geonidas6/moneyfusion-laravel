# Intégration MoneyFusion pour Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sefako/moneyfusion-laravel.svg?style=flat-square)](https://packagist.org/packages/sefako/moneyfusion-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/sefako/moneyfusion-laravel.svg?style=flat-square)](https://packagist.org/packages/sefako/moneyfusion-laravel)

Ce package fournit une intégration **complète et rapide** pour l'API de paiement MoneyFusion dans votre application Laravel. Il est conçu comme une solution **plug-and-play**, incluant les contrôleurs, les modèles et les vues nécessaires pour gérer les paiements (Pay-in) et les retraits (Payout).

## ✨ Features

*   📊 **Liste des Transactions** : Affichez et gérez l'historique de toutes les transactions.
*   🚀 **Plug-and-Play** : Publiez les contrôleurs, vues et migrations en une seule commande.
*   💳 **Paiements (Pay-in)** : Initiez des paiements et redirigez les utilisateurs.
*   💸 **Retraits (Payout)** : Demandez des transferts d'argent vers un numéro.
*   🔗 **Intégration Modèle `User`** : Liez les transactions à vos utilisateurs avec un simple Trait.
*   🔔 **Webhooks Gérés** : Une route et une logique de base pour traiter les notifications de MoneyFusion.
*   ✅ **Vérification de Statut** : Interrogez l'API pour connaître l'état d'une transaction.
*   🛠️ **Commande Artisan** : Vérifiez la connectivité avec l'API.

## 💾 Installation

Vous pouvez installer le package via Composer :

```bash
composer require sefako/moneyfusion-laravel
```

## ⚙️ Configuration

1.  **Publier les Fichiers (Recommandé)**

    Cette commande unique publiera la configuration, les contrôleurs, les vues et la migration. C'est la méthode la plus simple pour démarrer.

    ```bash
    php artisan vendor:publish --provider="Sefako\Moneyfusion\MoneyfusionServiceProvider"
    ```
    > Pour une publication plus fine (par exemple, uniquement le fichier de configuration), consultez la section [Publication des Ressources](#publication-des-ressources-détaillée).

2.  **Configurer les Variables d'Environnement**

    Ajoutez les informations de votre compte MoneyFusion à votre fichier `.env`.

    ```env
    MONEYFUSION_API_URL="https://www.pay.moneyfusion.net"
    MONEYFUSION_API_KEY="votre_cle_api_secrete"
    MONEYFUSION_MAKE_PAYMENT_API_URL="https://www.pay.moneyfusion.net/makePayment"
    ```

3.  **Exécuter la Migration**

    Cette commande créera la table `moneyfusion_transactions` dans votre base de données.

    ```bash
    php artisan migrate
    ```

## 🚀 Utilisation

Une fois configuré, vous avez plusieurs façons d'utiliser le package.

### Option 1 : La Voie Plug-and-Play (Après publication)

Si vous avez publié les fichiers à l'étape de configuration, des routes sont déjà prêtes. Assurez-vous que vos utilisateurs sont authentifiés, puis dirigez-les vers :

*   **Faire un paiement :** `/payment`
*   **Faire un retrait :** `/payout`

*   **Voir les transactions :** `/transactions`

### Option 2 : Intégration au Modèle `User`

C'est la méthode la plus élégante pour lier les transactions à vos utilisateurs.

1.  **Ajoutez le Trait** à votre modèle `User`.

    ```php
    // app/Models/User.php
    namespace App\Models;

    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Sefako\Moneyfusion\Concerns\HasMoneyfusionTransactions;

    class User extends Authenticatable
    {
        use HasMoneyfusionTransactions;
        // ...
    }
    ```

2.  **Accédez aux transactions** directement depuis une instance de l'utilisateur.

    ```php
    $user = User::find(1);

    // Obtenir toutes les transactions
    $allTransactions = $user->moneyfusionTransactions;

    // Obtenir uniquement les paiements (pay-ins)
    $payments = $user->moneyfusionPayments;

    // Obtenir uniquement les retraits (payouts)
    $payouts = $user->moneyfusionPayouts;
    ```

### Option 3 : Utilisation de la Façade (Avancé)

Pour un contrôle total, utilisez la façade `Moneyfusion` directement dans votre propre logique.

#### Initier un Paiement (Pay-in)

```php
use Sefako\Moneyfusion\Facades\Moneyfusion;

$paymentData = [
    'totalPrice' => 200,
    'numeroSend' => "01010101",
    'nomclient' => "John Doe",
    'return_url' => route('votre.route.callback'),
    'webhook_url' => route('moneyfusion.webhook'), // Le package enregistre cette route pour vous
];

$response = Moneyfusion::makePayment($paymentData);

if ($response && $response['statut'] === true) {
    // Associez la transaction à l'utilisateur si nécessaire
    auth()->user()->moneyfusionTransactions()->create([/* ... */]);
    return redirect()->away($response['url']);
}
```

#### Demander un Retrait (Payout)

```php
use Sefako\Moneyfusion\Facades\Moneyfusion;

$payoutData = [
    'montant' => 5000,
    'numero' => '01020304',
    'moyen' => 'orange', // 'orange', 'mtn', 'moov'
    'webhook_url' => route('moneyfusion.webhook'),
];

$response = Moneyfusion::requestPayout($payoutData);

if ($response && $response['statut'] === true) {
    // La demande de retrait a été acceptée
}
```

#### Vérifier le Statut

```php
// Pour un paiement
$status = Moneyfusion::checkPaymentStatus('token_du_paiement');

// Pour un retrait
$status = Moneyfusion::checkPayoutStatus('token_du_retrait');
```

## 🔔 Webhooks

Le package enregistre automatiquement une route (`/webhook`) et une logique pour traiter les notifications de MoneyFusion.

Lorsque vous publiez les fichiers du package, un modèle `MoneyfusionTransaction` est créé. Le webhook mettra à jour le statut de la transaction correspondante dans votre base de données.

Les événements suivants sont gérés :
-   `payin.session.completed`
-   `payin.session.cancelled`
-   `payout.session.completed`
-   `payout.session.cancelled`


## 📦 Publication des Ressources (Détaillée)

Vous pouvez choisir de ne publier que certaines parties du package en utilisant les tags suivants :

-   `--tag="moneyfusion-laravel-config"`
-   `--tag="moneyfusion-laravel-controllers"`
-   `--tag="moneyfusion-laravel-views"`
-   `--tag="moneyfusion-laravel-migrations"`

Exemple :
```bash
php artisan vendor:publish --provider="Sefako\Moneyfusion\MoneyfusionServiceProvider" --tag="moneyfusion-laravel-views"
```

## 🛠️ Commande Artisan

Une commande Artisan est incluse pour vérifier la connectivité avec l'API de MoneyFusion.

```bash
php artisan moneyfusion:check
```

## 🧪 Tests

Pour lancer les tests du package, exécutez la commande suivante depuis la racine de votre projet Laravel :

```bash
composer test
```

## 📜 Licence

Ce package est distribué sous la licence MIT. Voir le fichier `LICENSE` pour plus d'informations.

## 👏 Crédits

-   [Akotse Patrice](https://github.com/geonidas6)
