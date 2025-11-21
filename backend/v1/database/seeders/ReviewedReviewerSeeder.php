<?php

namespace Database\Seeders;

use App\Models\ReviewedReviewer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewedReviewerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SeederHelper::seedUsingFactory("reviewed_reviewer", "ReviewedReviewer");
    }
}
