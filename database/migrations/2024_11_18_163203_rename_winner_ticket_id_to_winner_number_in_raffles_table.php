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
            if (Schema::hasColumn('raffles', 'winner_ticket_id')) {
                $table->renameColumn('winner_ticket_id', 'winner_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('raffles', function (Blueprint $table) {
            if (Schema::hasColumn('raffles', 'winner_number')) {
                $table->renameColumn('winner_number', 'winner_ticket_id');
            }
        });
    }
};
