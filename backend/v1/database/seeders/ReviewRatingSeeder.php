<?php

namespace Database\Seeders;

use App\Models\ReviewRating;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewRatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SeederHelper::seedUsingFactory("review_ratings", "ReviewRating");
    }
}
