<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rent_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->cascadeOnDelete();
            $table->string('reference')->unique(); // ex: APP-2024-001
            $table->date('date_appel');
            $table->date('date_echeance');
            $table->decimal('montant', 12, 2);
            $table->decimal('montant_paye', 12, 2)->default(0);
            $table->enum('statut', ['en_attente', 'partiel', 'paye', 'en_retard'])->default('en_attente');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rent_calls');
    }
};
