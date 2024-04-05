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
        Schema::table('financial_transactions', function (Blueprint $table) {
            // Adiciona o campo 'identifier'
            $table->string('identifier')->nullable();

            // Altera os campos 'debit', 'credit' e 'note' para aceitarem valores nulos
            $table->double('debit')->nullable()->change();
            $table->double('credit')->nullable()->change();
            $table->string('note')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            // Reverte as alterações
            $table->dropColumn('identifier');
            $table->double('debit')->nullable(false)->change();
            $table->double('credit')->nullable(false)->change();
            $table->string('note')->nullable(false)->change();
        });
    }
};
