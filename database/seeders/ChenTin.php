<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChenTin extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 20; $i++) {
            DB::table('posts')->insert([
                ['idcatePosts'=>1 ,'titlePosts' => rand(), 'slugPosts' => Str::slug(rand()), 'summaryPosts' => rand(), 'contentPosts' => rand(), 'author' => 'tmp', 'created_at' => now()],
            ]);
        }
    }
}
