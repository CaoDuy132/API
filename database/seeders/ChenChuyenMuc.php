<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChenChuyenMuc extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cate_posts')->insert([
            ['nameCatePosts'=>'Mẹo vặt','slugCatePost'=>'meo-vat','created_at'=>now()],
            ['nameCatePosts'=>'Thời trang 24/7','slugCatePost'=>'thoi-trang-247','created_at'=>now()],
        ]);
    }
}
