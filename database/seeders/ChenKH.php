<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChenKH extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('customers')->insert([
            'name'=>"Trương Minh Phụng ",
            'phone' => '0962761246',
            'email'=>'tmpdz7820@gmail.com',
            'password'=>bcrypt(123),
            'created_at'=>now()
        ]);
    }
}
