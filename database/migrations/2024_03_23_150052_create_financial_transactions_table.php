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
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable(false);
            $table->string('description')->nullable(false);
            $table->double('credit')->nullable(false);
            $table->double('debit')->nullable(false);
            $table->string('note');
            $table->unsignedBigInteger('financial_service_id')->nullable(false);
            $table->auditable();
            $table->timestamps();

            // Define a foreign key constraint
            $table->foreign('financial_service_id')->references('id')->on('financial_services');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
