<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moneyfusion_transactions', function (Blueprint $table) {
            $table->id();
            // Ajout de la colonne user_id
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Ou cascade, selon votre logique

            $table->string('token_pay')->unique();
            $table->string('numero_send');
            $table->string('nom_client');
            $table->json('articles');
            $table->decimal('total_price', 10, 2);
            $table->json('personal_info')->nullable();
            $table->string('return_url')->nullable();
            $table->string('webhook_url')->nullable();
            $table->string('status')->default('pending'); // pending, failure, no paid, paid
            $table->string('transaction_number')->nullable();
            $table->decimal('fees', 8, 2)->nullable();
            $table->string('payment_method')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moneyfusion_transactions');
    }
};