<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('raffles', function (Blueprint $table) {
            // Cambiar el tipo de 'status' a TINYINT para usarlo como booleano
            $table->tinyInteger('status')->default(0); // 0 para 'activo', 1 para 'finalizado'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('raffles', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};

