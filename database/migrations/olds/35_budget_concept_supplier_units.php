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
        Schema::create('budget_concept_supplier_units', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('budget_concept_id')->nullable();
            $table->integer('units')->nullable();
            $table->tinyInteger('selected')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('budget_concept_id')->references('id')->on('budget_concepts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_concept_supplier_units');

    }
};
