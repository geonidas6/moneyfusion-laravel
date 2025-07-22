-----

## 📦 MoneyFusion Laravel Package

[](https://www.google.com/search?q=https://packagist.org/packages/your-vendor-name/moneyfusion-laravel)
[](https://www.google.com/search?q=https://packagist.org/packages/your-vendor-name/moneyfusion-laravel)
[](https://www.google.com/search?q=https://github.com/your-vendor-name/moneyfusion-laravel/blob/main/LICENSE)

A comprehensive Laravel package to effortlessly integrate with the MoneyFusion payment gateway. This package provides a seamless experience for processing payments, handling webhooks, generating invoices, and managing transaction statuses within your Laravel application.

-----

### ✨ Features

* **Easy Payment Integration**: Process payments through MoneyFusion with a simple form and API calls.
* **Automated Webhook Handling**: Automatically update transaction statuses based on MoneyFusion webhooks.
* **Transaction Management**: Store and manage MoneyFusion transactions in your database.
* **User Association**: Link transactions directly to your application's users.
* **Invoice Generation**: Generate and download PDF invoices for successful payments.
* **Scheduled Status Checks**: Periodically verify the status of pending transactions to ensure data consistency.
* **Email Notifications**: Send automated email notifications for payment successes and failures.
* **Multi-language Support**: Localized messages and views for a global audience.
* **Robust Error Logging**: Enhanced logging for MoneyFusion API interactions and webhook processing.

-----

### 🚀 Installation

You can install the package via Composer:

```bash
composer require your-vendor-name/moneyfusion-laravel
```

-----

### ⚙️ Configuration & Setup

After installation, you'll need to publish the package's assets and configure your environment.

1.  **Publish Assets:**

    Run the following Artisan commands to publish the configuration, migrations, views, and language files:

    ```bash
    php artisan vendor:publish --provider="Vendor\MoneyFusion\MoneyFusionServiceProvider" --tag="moneyfusion-config"
    php artisan vendor:publish --provider="Vendor\MoneyFusion\MoneyFusionServiceProvider" --tag="moneyfusion-migrations"
    php artisan vendor:publish --provider="Vendor\MoneyFusion\MoneyFusionServiceProvider" --tag="moneyfusion-views"
    php artisan vendor:publish --provider="Vendor\MoneyFusion\MoneyFusionServiceProvider" --tag="moneyfusion-lang"
    php artisan vendor:publish --provider="Vendor\MoneyFusion\MoneyFusionServiceProvider" --tag="moneyfusion-emails"
    ```

2.  **Run Migrations:**

    This will create the `moneyfusion_transactions` table in your database.

    ```bash
    php artisan migrate
    ```

3.  **Environment Variables:**

    Add the following variables to your `.env` file and replace the placeholders with your actual MoneyFusion API credentials and details.

    ```dotenv
    MONEYFUSION_API_URL="https://your-moneyfusion-api-url.com/api" # e.g., https://moneyfusion.example.com/api/v1
    MONEYFUSION_API_KEY="your_api_key_here"
    MONEYFUSION_WEBHOOK_SECRET="your_webhook_secret_here" # Crucial for webhook signature verification
    MONEYFUSION_PDF_DRIVER="dompdf" # or 'snappy' if you configure it

    # Company Billing Details (for invoices)
    MONEYFUSION_BILLING_COMPANY_NAME="Your Company Name"
    MONEYFUSION_BILLING_COMPANY_ADDRESS="123 Example Street"
    MONEYFUSION_BILLING_COMPANY_CITY_ZIP="75000 Paris"
    MONEYFUSION_BILLING_COMPANY_COUNTRY="France"
    MONEYFUSION_BILLING_COMPANY_EMAIL="contact@yourcompany.com"
    MONEYFUSION_BILLING_COMPANY_PHONE="+33 1 23 45 67 89"
    MONEYFUSION_BILLING_INVOICE_PREFIX="INV-"
    ```

4.  **Schedule Pending Payments Check:**

    To ensure transactions in `pending` status are eventually updated, add the following to your `app/Console/Kernel.php` file within the `schedule` method:

    ```php
    // app/Console/Kernel.php

    protected function schedule(Schedule $schedule): void
    {
        // ... other scheduled commands
        $schedule->command('moneyfusion:check-pending')->everyFifteenMinutes(); // Or your preferred frequency
    }
    ```

    Make sure your system's cron job is set up to run Laravel's scheduler:

    ```bash
    * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
    ```

-----

### 💻 Usage

#### Payment Form

To display the payment form, you can link to the package's route:

```blade
<a href="{{ route('moneyfusion.form') }}" class="btn btn-primary">Pay with MoneyFusion</a>
```

You can customize the payment form view by modifying `resources/views/vendor/moneyfusion/components/payment-form.blade.php` after publishing the views.

#### Handling Callbacks

The package automatically handles MoneyFusion callbacks at the configured `moneyfusion.routes.callback_path` (default is `/moneyfusion/callback`). After a payment attempt, MoneyFusion will redirect the user back to this URL.

#### Webhook Listener

The package listens for MoneyFusion webhooks at the configured `moneyfusion.routes.webhook_path` (default is `/moneyfusion/webhook`). You **must** register this webhook URL in your MoneyFusion merchant dashboard to receive real-time payment status updates.

#### Viewing Transactions & Invoices

Users can view their past transactions and download invoices. Ensure the user is authenticated as `user_id` is used to filter transactions.

```blade
<a href="{{ route('moneyfusion.invoices.index') }}" class="btn btn-secondary">My Transactions & Invoices</a>
```

* **List all transactions**: `route('moneyfusion.invoices.index')`
* **View a specific transaction**: `route('moneyfusion.invoices.show', $transaction)`
* **Download an invoice**: `route('moneyfusion.invoices.download', $transaction)` (Only for 'paid' transactions)

#### Sending Payment Notifications Manually (Optional)

While the package automatically sends notifications, you can trigger them manually if needed:

```php
use Vendor\MoneyFusion\Mail\PaymentStatusNotification;
use Vendor\MoneyFusion\Models\MoneyFusionTransaction;
use Illuminate\Support\Facades\Mail;
use App\Models\User; // Your application's User model

// Assuming you have a transaction and a user
$transaction = MoneyFusionTransaction::find($transactionId);
$user = User::find($userId); // Or $transaction->user if eager loaded

if ($user && $user->email && $transaction) {
    // For successful payment
    Mail::to($user->email)->send(new PaymentStatusNotification($transaction, 'success'));

    // For failed payment
    // Mail::to($user->email)->send(new PaymentStatusNotification($transaction, 'failure'));
}
```

-----

### 🌍 Localization

The package supports localization for its messages and views. After publishing the language files (`php artisan vendor:publish --tag="moneyfusion-lang"`), you can find them in `resources/lang/vendor/moneyfusion`.

To add a new language, create a new directory (e.g., `es` for Spanish) and copy the `moneyfusion.php` file into it, then translate the strings.

-----

### 🛡️ Security

* **Webhook Signature Verification**: The package includes a middleware (`VerifyMoneyFusionWebhookSignature`) to verify the authenticity of incoming webhooks using `MONEYFUSION_WEBHOOK_SECRET`. **Ensure this secret is configured and matches the one in your MoneyFusion dashboard.** The actual signature verification logic needs to be implemented based on MoneyFusion's specific signing method (e.g., HMAC-SHA256).
* **User Authorization**: All invoice and transaction viewing routes are protected to ensure users can only access their own data.

-----

### 🤝 Contributing

Contributions are welcome\! Please feel free to open an issue or submit a pull request on the [GitHub repository](https://www.google.com/search?q=https://github.com/your-vendor-name/moneyfusion-laravel).

-----

### 📄 License

The MoneyFusion Laravel package is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

-----

### 📧 Support

If you encounter any issues or have questions, please open an issue on the [GitHub repository](https://www.google.com/search?q=https://github.com/your-vendor-name/moneyfusion-laravel/issues) or contact [your.email@example.com](mailto:your.email@example.com).

-----