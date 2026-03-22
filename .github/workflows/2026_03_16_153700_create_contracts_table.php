<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('contracts'); // ← sécurité

        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('property_id');
            $table->enum('type', ['location', 'vente'])->default('location');
            $table->enum('statut', ['actif', 'termine', 'resilie', 'en_attente'])->default('en_attente');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->decimal('loyer_mensuel', 12, 2);
            $table->decimal('caution', 12, 2)->default(0);
            $table->integer('jour_paiement')->default(1);
            $table->enum('periodicite', ['mensuel', 'trimestriel', 'annuel'])->default('mensuel');
            $table->text('conditions')->nullable();
            $table->string('document')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Clés étrangères — une seule fois
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('property_id')->references('id')->on('properties')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
