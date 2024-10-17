<?php

namespace Database\Seeders;

use App\Models\Donation;
use App\Models\User;
use App\Models\UserDonation;
use Faker\Generator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserDonationSeeder extends Seeder
{
    private $users;
    private $donations;

    public function __construct()
    {
        $this->users = User::all()->pluck('id');
        $this->donations = Donation::all()->pluck('id');
    }

    /**
     * Run the database seeds.
     */
    public function run(Generator $generator): void
    {
        for ($i = 0; $i < 100; $i++) {
            UserDonation::create([
                'amount_given' => $generator->numberBetween([50000, 250000]),
                'donation_id' => $generator->randomElement($this->donations),
                'user_id'     => $generator->randomElement($this->users)
            ]);
        }
    }
}
