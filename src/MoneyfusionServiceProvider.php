<?php

namespace Sefako\Moneyfusion;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Sefako\Moneyfusion\Commands\MoneyfusionCommand;

class MoneyfusionServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('moneyfusion-laravel')
            ->hasConfigFile('moneyfusion')
            ->hasViews('moneyfusion')
            ->hasMigration('create_moneyfusion_transactions_table')
            ->hasRoute('web')
            ->hasCommand(MoneyfusionCommand::class);

        $this->publishes([
            __DIR__ . '/Http/Controllers' => app_path('Http/Controllers/Moneyfusion'),
        ], 'moneyfusion-laravel-controllers');

        $this->publishes([
            __DIR__ . '/Models' => app_path('Models'),
        ], 'moneyfusion-laravel-models');
    }
}
