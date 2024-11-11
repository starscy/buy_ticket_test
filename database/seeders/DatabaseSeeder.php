<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Event::create([
            'name' => 'Бразилия',
            'description' => 'Поездка в Бразилию',
            'date' => '2021-08-21 13:00:00',
            'prices' => json_encode([
                'adult' => 700,
                'kid' => 450
            ]),
        ]);

        Event::create([
            'name' => 'Китай',
            'description' => 'Поездка в Китай',
            'date' => '2021-08-21 13:00:00',
            'prices' => json_encode([
                'adult' => 600,
                'kid' => 350
            ]),
        ]);
    }
}
