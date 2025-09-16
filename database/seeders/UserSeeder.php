<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'user@spendlapse.com'],
            [
                'name' => 'Darren Watkins',
                'password' => Hash::make('password'),
                'bank_preference' => 'CIMB',
            ]
        );
    }
}