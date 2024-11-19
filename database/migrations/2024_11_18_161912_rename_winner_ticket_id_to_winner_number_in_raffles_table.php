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
            // Cambiamos el nombre de la columna
            $table->renameColumn('winner_ticket_id', 'winner_number');

            // Cambiamos el tipo de dato si es necesario
            // $table->integer('winner_number')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('raffles', function (Blueprint $table) {
            // Revertimos el cambio
            $table->renameColumn('winner_number', 'winner_ticket_id');
        });
    }
};
