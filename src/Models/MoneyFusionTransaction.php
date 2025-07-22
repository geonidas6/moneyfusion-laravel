<?php

namespace Vendor\MoneyFusion\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // N'oubliez pas d'importer ceci
use App\Models\User; // Assurez-vous d'importer le modèle User de votre application

class MoneyFusionTransaction extends Model
{
    use HasFactory;

    protected $table = 'moneyfusion_transactions';

    protected $fillable = [
        'user_id', // Ajouté
        'token_pay',
        'numero_send',
        'nom_client',
        'articles',
        'total_price',
        'personal_info',
        'return_url',
        'webhook_url',
        'status',
        'transaction_number',
        'fees',
        'payment_method',
    ];

    protected $casts = [
        'articles' => 'array',
        'personal_info' => 'array',
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}