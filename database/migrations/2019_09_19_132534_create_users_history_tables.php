<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersHistoryTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_recharge_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('company_id')->nullable()->unsigned();
            $table->integer('vendor_id')->nullable()->unsigned();
            $table->float('amount');
            $table->boolean('pending')->default(1);
            $table->timestamps();
        });

        Schema::table('users_recharge_history', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('vendor_id')->references('id')->on('users');
            $table->foreign('company_id')->references('id')->on('companies');
        });

        Schema::create('users_purchase_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('company_id')->unsigned()->nullable();
            $table->string('fuel_name');
            $table->float('fuel_cost_price');
            $table->float('fuel_price');
            $table->float('amount');
            $table->boolean('cancelled')->default(0);
            $table->boolean('confirmed')->default(0);
            $table->string('purchase_code')->nullable();
            $table->timestamps();
        });

        Schema::table('users_purchase_history', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
