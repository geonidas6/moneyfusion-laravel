<?php

namespace Vendor\MoneyFusion\Console\Commands;

use Illuminate\Console\Command;
use Vendor\MoneyFusion\Models\MoneyFusionTransaction;
use Vendor\MoneyFusion\Services\MoneyFusionService;
use Illuminate\Support\Facades\Log;

class CheckPendingPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moneyfusion:check-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks the status of pending MoneyFusion transactions.';

    protected MoneyFusionService $moneyFusionService;

    public function __construct(MoneyFusionService $moneyFusionService)
    {
        parent::__construct();
        $this->moneyFusionService = $moneyFusionService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking pending MoneyFusion transactions...');

        $pendingTransactions = MoneyFusionTransaction::where('status', 'pending')
            ->where('created_at', '<', now()->subMinutes(10)) // Vérifier les transactions en attente depuis au moins 10 minutes
            ->get();

        if ($pendingTransactions->isEmpty()) {
            $this->info('No pending transactions to check.');
            return 0;
        }

        foreach ($pendingTransactions as $transaction) {
            try {
                $statusResponse = $this->moneyFusionService->checkPaymentStatus($transaction->token_pay);

                if ($statusResponse['statut']) {
                    $moneyFusionStatus = $statusResponse['data']['statut'];

                    if ($transaction->status !== $moneyFusionStatus) {
                        $transaction->update([
                            'status' => $moneyFusionStatus,
                            'transaction_number' => $statusResponse['data']['numeroTransaction'] ?? $transaction->transaction_number,
                            'fees' => $statusResponse['data']['frais'] ?? $transaction->fees,
                            'payment_method' => $statusResponse['data']['moyen'] ?? $transaction->payment_method,
                        ]);
                        $this->info("Transaction {$transaction->token_pay} updated to status: {$moneyFusionStatus}");
                        Log::info("MoneyFusion: Pending transaction {$transaction->token_pay} status updated to {$moneyFusionStatus} via scheduled command.");
                    } else {
                        $this->line("Transaction {$transaction->token_pay} is still {$moneyFusionStatus}. No update needed.");
                    }
                } else {
                    $this->warn("Could not get status for transaction {$transaction->token_pay}: " . ($statusResponse['message'] ?? 'Unknown error'));
                    Log::warning("MoneyFusion: Failed to get status for pending transaction {$transaction->token_pay}. Message: " . ($statusResponse['message'] ?? 'Unknown error'));
                }
            } catch (\Exception $e) {
                $this->error("Error checking transaction {$transaction->token_pay}: " . $e->getMessage());
                Log::error("MoneyFusion: Error checking pending transaction {$transaction->token_pay}: " . $e->getMessage(), ['exception' => $e]);
            }
        }

        $this->info('Pending transactions check completed.');
        return 0;
    }
}