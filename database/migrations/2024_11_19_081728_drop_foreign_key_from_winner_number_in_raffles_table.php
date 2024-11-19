<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropForeignKeyFromWinnerNumberInRafflesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('raffles', function (Blueprint $table) {
            // Eliminar la clave foránea
            $table->dropForeign(['winner_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('raffles', function (Blueprint $table) {
            // Restaurar la clave foránea si es necesario
            $table->foreign('winner_number')
                  ->references('id')->on('tickets')
                  ->onDelete('set null');
        });
    }
}

