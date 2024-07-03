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
        Schema::create('company_passwords', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('title')->nullable();
            $table->string('website')->nullable();
            $table->string('user')->nullable();
            $table->string('password')->nullable();
     

            $table->foreign('client_id')->references('id')->on('clients');
 
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
