<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('receipts');

        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rent_call_id');
            $table->unsignedBigInteger('contract_id');
            $table->string('reference')->unique();
            $table->date('date_paiement');
            $table->decimal('montant', 12, 2);
            $table->enum('mode_paiement', ['especes', 'virement', 'cheque', 'mobile_money'])->default('especes');
            $table->string('numero_transaction')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('rent_call_id')->references('id')->on('rent_calls')->cascadeOnDelete();
            $table->foreign('contract_id')->references('id')->on('contracts')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
