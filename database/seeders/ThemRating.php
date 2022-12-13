<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ThemRating extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $star = [1, 2, 3, 4, 5];



        for ($i = 1; $i <= 30; $i++) {
            DB::table('rating_prod')->insert([
                "idProd" => 10,
                "idCustomer" => 4,
                "num_star" => Arr::random($star),
                "content_review" => md5(rand()) . uniqid(),
                "status"=>2,
                'created_at' => now()
            ]);
        }
    }
}
