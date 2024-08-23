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

        Schema::create('associated_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained('budgets')->onDelete('cascade')->nullable();
            $table->foreignId('bank_id')->constrained('bank_accounts')->onDelete('cascade')->nullable();
            $table->foreignId('purchase_order_id')->constrained('purchase_order')->onDelete('cascade')->nullable();
            $table->foreignId('payment_method_id')->constrained('payment_method')->onDelete('cascade')->nullable();
            $table->string('title')->collation('utf8_unicode_ci')->nullable();
            $table->float('quantity',10,2)->nullable();
            $table->date('received_date')->nullable();
            $table->date('date')->nullable();
            $table->string('reference')->nullable();
            $table->enum('state',['PAGADO','PENDIENTE']);
            $table->tinyInteger('aceptado_gestor')->nullable();


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
