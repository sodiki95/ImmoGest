<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('charges');

        Schema::create('charges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->string('titre');
            $table->enum('categorie', [
                'entretien',
                'reparation',
                'taxe',
                'assurance',
                'syndic',
                'eau',
                'electricite',
                'internet',
                'gardiennage',
                'autre'
            ])->default('autre');
            $table->enum('statut', ['en_attente', 'paye', 'annule'])->default('en_attente');
            $table->enum('periodicite', ['unique', 'mensuel', 'trimestriel', 'annuel'])->default('unique');
            $table->decimal('montant', 12, 2);
            $table->date('date_charge');
            $table->date('date_paiement')->nullable();
            $table->string('fournisseur')->nullable();
            $table->string('numero_facture')->nullable();
            $table->string('document')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('property_id')->references('id')->on('properties')->cascadeOnDelete();
            $table->foreign('owner_id')->references('id')->on('owners')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('charges');
    }
};
