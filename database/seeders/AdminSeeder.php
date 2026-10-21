<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => '1daw@dawscape.com'],
            [
                'name' => '1daw',
                'password' => bcrypt('1daw'),
                'email_verified_at' => now(),
            ]
        );
    }
}
