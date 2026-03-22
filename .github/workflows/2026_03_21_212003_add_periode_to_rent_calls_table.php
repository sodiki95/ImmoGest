<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rent_calls', function (Blueprint $table) {
            //
            $table->date('periode')->after('contract_id');
            //table->integer('montant_appel', 10)->after('date_echeance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rent_calls', function (Blueprint $table) {
            //
        });
    }
};
