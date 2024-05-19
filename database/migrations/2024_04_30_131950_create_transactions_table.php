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
            $table->string('cheqno')->nullable();
            $table->string('cheqbank')->nullable();
            $table->decimal('cheqamt', 10, 2);
            $table->date('cheqdate');
            $table->string('trans_type');
            $table->string('narration');
            $table->date('depositdate')->nullable();
            $table->string('cheqstatus')->nullable();
            $table->string('depositac')->nullable();
            $table->string('remarks')->nullable();
            $table->string('cheq_img')->nullable();
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
