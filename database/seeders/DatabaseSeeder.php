<?php

namespace Database\Seeders;
use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\TimeLog;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()
        ->count(3)
        ->has(
            Client::factory()
                ->count(2)
                ->has(
                    Project::factory()
                        ->count(2)
                        ->has(TimeLog::factory()->count(4))
                )
        )
        ->create();
    }
}
