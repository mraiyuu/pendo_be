<?php

namespace Database\Seeders;

use App\Models\Task;
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
            'user_id'=>'6d9cd035-c2cb-43a4-b4fd-9244ea036821',
            'password'=>\Hash::make('12345678'),
            // 'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Task::factory(40)->create([
            // 'task_id'=>fake()->uuid(),
            'user_id'=>'6d9cd035-c2cb-43a4-b4fd-9244ea036821',
            // 'title'=>fake()->sentence(),
            // 'description'=>fake()->paragraph(),
            // 'due_date'=>fake()->dateTime(),
        ]);
    }
}
