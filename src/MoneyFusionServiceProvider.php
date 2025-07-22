<?php

namespace Vendor\MoneyFusion;

use Illuminate\Support\ServiceProvider;
use Vendor\MoneyFusion\Services\MoneyFusionService;
use Vendor\MoneyFusion\Facades\MoneyFusion; // Assurez-vous d'importer la façade

class MoneyFusionServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/moneyfusion.php', 'moneyfusion'
        );

        $this->app->singleton('moneyfusion', function ($app) {
            return new MoneyFusionService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Publier la configuration
        $this->publishes([
            __DIR__.'/../config/moneyfusion.php' => config_path('moneyfusion.php'),
        ], 'moneyfusion-config');

        // Publier les migrations
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'moneyfusion-migrations');

        // Publier les vues
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/moneyfusion'),
        ], 'moneyfusion-views');

        // Charger les routes du package
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');


        // Publier les fichiers de langue
        $this->publishes([
            __DIR__.'/../resources/lang' => $this->app->langPath('vendor/moneyfusion'),
        ], 'moneyfusion-lang');

        // Charger les fichiers de langue
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'moneyfusion');



        if ($this->app->runningInConsole()) {
            $this->commands([
                CheckPendingPayments::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/../resources/views/emails' => resource_path('views/vendor/moneyfusion/emails'),
        ], 'moneyfusion-emails'); // Nouveau tag pour les emails

    }
}