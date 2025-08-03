<?php

namespace Sefako\Moneyfusion\Commands;

use Illuminate\Console\Command;
use Sefako\Moneyfusion\Facades\Moneyfusion;

class MoneyfusionCommand extends Command
{
    public $signature = 'moneyfusion:check';

    public $description = 'Check the connection to the MoneyFusion API.';

    public function handle(): int
    {
        $this->info('Checking connection to MoneyFusion API...');

        // Use a sample token for testing
        $response = Moneyfusion::checkPaymentStatus('sample-token');

        if ($response) {
            $this->info('Successfully connected to MoneyFusion API.');
            $this->comment(json_encode($response, JSON_PRETTY_PRINT));
        } else {
            $this->error('Failed to connect to MoneyFusion API.');
        }

        return self::SUCCESS;
    }
}