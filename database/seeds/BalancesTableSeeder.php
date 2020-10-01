<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BalancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       DB::table('balances')->insert([
            'user_id' => 6,
        ]);
    }
}
