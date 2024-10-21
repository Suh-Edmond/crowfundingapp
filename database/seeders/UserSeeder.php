<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Generator $generator): void
    {
        for ($i = 0; $i < 5; $i++) {
            User::create([
                'name' => $generator->name,
                'email' => $generator->email,
                'password' => Hash::make("password"),
                'telephone' => $generator->phoneNumber
            ]);
        }
    }
}
