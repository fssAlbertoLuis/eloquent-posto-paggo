<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('cnpj')->nullable();
            $table->float('total_earnings')->default(0);
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
        Schema::dropIfExists('monthly_statistics');
        Schema::dropIfExists('users_purchase_history');
        Schema::dropIfExists('users_recharge_history');
        Schema::dropIfExists('balances');
        Schema::dropIfExists('users');
        Schema::dropIfExists('fuel_list');
        Schema::dropIfExists('companies');
    }
}
