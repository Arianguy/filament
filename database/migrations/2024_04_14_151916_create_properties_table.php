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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('class');
            $table->string('type');
            $table->date('purchase_date');
            $table->string('title_deed_no')->unique();
            $table->string('mortgage_status');
            $table->string('community');
            $table->unsignedBigInteger('plot_no');
            $table->unsignedBigInteger('bldg_no');
            $table->string('bldg_name');
            $table->unsignedBigInteger('property_no');
            $table->string('floor_detail');
            $table->decimal('suite_area', 10, 2);
            $table->decimal('balcony_area', 10, 2);
            $table->decimal('area_sq_mter', 10, 2);
            $table->decimal('common_area', 10, 2);
            $table->decimal('area_sq_feet', 10, 2);
            $table->foreignId('owner_id')
                ->constrained('owners')
                ->cascadeOnDelete();
            $table->unsignedBigInteger("purchase_value");
            $table->unsignedBigInteger("dewa_premise_no")
                ->nullable()
                ->unique();
            $table->unsignedBigInteger("dewa_account_no")->nullable();
            $table->string('status');
            $table->string('salesdeed');
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
