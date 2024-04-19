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
            $table->date('purchasedate');
            $table->string('titledeedno')->unique();
            $table->string('mortgage');
            $table->string('community');
            $table->unsignedBigInteger('plotno');
            $table->unsignedBigInteger('bldgno');
            $table->string('bldgname');
            $table->unsignedBigInteger('propertyno');
            $table->string('floordetail');
            $table->decimal('suitearea', 10, 2);
            $table->decimal('balconyarea', 10, 2);
            $table->decimal('areasqmter', 10, 2);
            $table->decimal('commonarea', 10, 2);
            $table->decimal('areasqfeet', 10, 2);
            $table->foreignId('ownerid')
                ->constrained('owners')
                ->cascadeOnDelete();
            $table->unsignedBigInteger("purchasevalue");
            $table->unsignedBigInteger("dewapremiseno")->nullable();
            $table->unsignedBigInteger("dewaacno")->nullable();
            $table->string('status');
            $table->string('salesdeed');
            $table->boolean('is_visible')->default(false);
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
