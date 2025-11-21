<?php

namespace Database\Seeders;

use App\Models\ProfileView;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfileViewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SeederHelper::seedUsingFactory("profile_views", "ProfileView");
    }
}
