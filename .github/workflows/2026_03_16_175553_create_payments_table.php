<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('payments');

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id');
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('property_id');
            $table->string('reference')->unique();
            $table->date('periode'); // mois concerné
            $table->date('date_echeance');
            $table->date('date_paiement')->nullable();
            $table->decimal('montant_du', 12, 2);
            $table->decimal('montant_paye', 12, 2)->default(0);
            $table->decimal('penalite', 12, 2)->default(0);
            $table->enum('statut', ['en_attente', 'paye', 'partiel', 'impaye', 'en_retard'])->default('en_attente');
            $table->enum('mode_paiement', ['especes', 'virement', 'cheque', 'mobile_money'])->nullable();
            $table->string('numero_transaction')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('contract_id')->references('id')->on('contracts')->cascadeOnDelete();
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('property_id')->references('id')->on('properties')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
