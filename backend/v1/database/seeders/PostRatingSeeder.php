<?php

namespace Database\Seeders;

use App\Models\PostRating;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostRatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SeederHelper::seedUsingFactory("post_ratings", "PostRating");
    }
}
