<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'company_id' => 1,
                'name' => 'Gerente geral vital',
                'email' => 'ggvital@admin.com',
                'phone' =>null,
                'phone_verified' => false,
                'password' => bcrypt('adminadmin'),
                'permission' => 6,
                'is_proprietor' => true,
            ],
            [
                'company_id' => 1,
                'name' => 'Gerente vital',
                'email' => 'gvital@admin.com',
                'phone' => null,
                'phone_verified' => false,
                'password' => bcrypt('adminadmin'),
                'permission' => 5,
                'is_proprietor' => false,
            ],
            [
                'company_id' => 2,
                'name' => 'Gerente geral',
                'email' => 'gerentegeral@email.com',
                'phone' => null,
                'phone_verified' => false,
                'password' => bcrypt('gerentegeral'),
                'permission' => 4,
                'is_proprietor' => false,
            ],
            [
                'company_id' => 2,
                'name' => 'Primeiro gerente',
                'email' => 'gerente@email.com',
                'phone' => null,
                'phone_verified' => false,
                'password' => bcrypt('gerente'),
                'permission' => 3,
                'is_proprietor' => false,
            ],
            [
                'company_id' => 2,
                'name' => 'Primeiro frentista',
                'email' => 'frentista@email.com',
                'phone' => null,
                'phone_verified' => false,
                'password' => bcrypt('frentista'),
                'permission' => 2,
                'is_proprietor' => false,
            ],
            [
                'company_id' => null,
                'name' => 'Primeiro cliente',
                'email' => 'cliente@email.com',
                'phone' => '6999999999',
                'phone_verified' => true,
                'password' => bcrypt('cliente'),
                'permission' => 1,
                'is_proprietor' => false,
            ],
        ]);
    }
}
