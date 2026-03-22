<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->enum('type', ['appartement', 'maison', 'villa', 'studio', 'bureau', 'terrain']);
            $table->enum('statut', ['disponible', 'loue', 'en_vente', 'vendu'])->default('disponible');
            $table->text('description')->nullable();
            $table->string('adresse');
            $table->string('ville');
            $table->string('code_postal', 10);
            $table->decimal('superficie', 8, 2); // en m²
            $table->integer('nb_pieces')->nullable();
            $table->integer('nb_chambres')->nullable();
            $table->decimal('prix', 12, 2); // loyer ou prix de vente
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
