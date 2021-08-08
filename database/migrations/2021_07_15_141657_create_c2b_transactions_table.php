<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateC2bTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c2b_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('transaction_type')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('transaction_time')->nullable();
            $table->string('business_short_code')->nullable();
            $table->string('bill_ref_number')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('third_party_transaction_id')->nullable();
            $table->string('phone_number')->nullable();
            $table->decimal('amount', 8, 2)->nullable();
            $table->decimal('organization_acc_balance', 8, 2)->nullable();
            $table->string('status')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('c2b_transactions');
    }
}
