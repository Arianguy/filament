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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')
                ->constrained('contracts')
                ->cascadedOnDelete();
            $table->string('paytype');
            $table->string('cheqno');
            $table->string('cheqbank');
            $table->decimal('cheqamt', 10, 2);
            $table->date('cheqdate');
            $table->string('trans_type');
            $table->string('narration');
            $table->date('depositdate');
            $table->string('cheqstatus');
            $table->string('depositac');
            $table->string('remarks');
            $table->string('cheq_img');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
