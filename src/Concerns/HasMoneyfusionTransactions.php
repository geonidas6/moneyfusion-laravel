<?php

namespace Sefako\Moneyfusion\Concerns;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Sefako\Moneyfusion\Models\MoneyfusionTransaction;

/**
 * Trait HasMoneyfusionTransactions
 * @package Sefako\Moneyfusion\Concerns
 */
trait HasMoneyfusionTransactions
{
    /**
     * Get all of the transactions for the user.
     */
    public function moneyfusionTransactions(): HasMany
    {
        return $this->hasMany(MoneyfusionTransaction::class, 'user_id', 'id');
    }

    /**
     * Get only the payment transactions for the user.
     */
    public function moneyfusionPayments()
    {
        return $this->moneyfusionTransactions()->where('type', 'payment');
    }

    /**
     * Get only the payout transactions for the user.
     */
    public function moneyfusionPayouts()
    {
        return $this->moneyfusionTransactions()->where('type', 'payout');
    }
}
