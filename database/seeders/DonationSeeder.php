<?php

namespace Database\Seeders;

use App\Constants\DonationCategory;
use App\Constants\DonationStatus;
use App\Models\Donation;
use App\Models\User;
use Carbon\Carbon;
use Faker\Generator;
use Illuminate\Database\Seeder;

class DonationSeeder extends Seeder
{
    private $users;

    public function __construct()
    {
        $this->users = User::all()->pluck('id');
    }

    /**
     * Run the database seeds.
     */
    public function run(Generator $generator): void
    {
        for ($i = 0; $i < 3; $i++) {
            Donation::create([
                'title' => $generator->sentence(4),
                'description' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum",
                'estimated_amount' => $generator->numberBetween([50000, 100000]),
                'status' => $generator->randomElement([DonationStatus::INCOMPLETE, DonationStatus::COMPLETE]),
                'deadline' => Carbon::now(),
                'user_id' => $generator->randomElement($this->users),
                'category' => $generator->randomElement([DonationCategory::HUMANITARIAN, DonationCategory::EVANGELISM, DonationCategory::REFUGEE])
            ]);
        }
    }
}
