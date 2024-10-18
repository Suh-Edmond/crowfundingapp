<?php

namespace Tests\Feature;

use App\Constants\DonationCategory;
use App\Constants\DonationStatus;
use App\Models\Donation;
use App\Models\User;
use App\Models\UserDonation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserDonationsControllerTest extends TestCase
{
    use RefreshDatabase;
    private User $user;
    private Donation $donation;
    protected function setUp(): void
    {
        parent::setUp();


        $this->user = User::factory([
            'email' => "johndoe@gmail.com",
            "name" => "John Doe",
            "telephone" => "64567456",
            "password" => Hash::make("password")
        ])->create();

        $this->be($this->user);

        $this->donation = Donation::create([
            'title'             => "example title",
            'description'       => "example description",
            'estimated_amount'  => 10000,
            'user_id'           => $this->user->id,
            'status'            => DonationStatus::INCOMPLETE,
            'deadline'          => '2024-12-01 00:00:00',
            'category'          => DonationCategory::REFUGEE
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }


    public function test_addDonation_return_422_validation_exception(): void
    {
        $this->json('POST', '/api/protected/donations/add_contribute',['Accept' => "application/json", 'Content-Type'=>'application/json'], [
            'title' =>  '',
            'description' => '',
            'deadline' => '',
            'estimated_amount' => '0'
        ])->assertStatus(422)
            ->assertJson([
                "message" => "The donation id field is required. (and 1 more error)",
                "errors" => [
                    "donation_id" =>["The donation id field is required."],
                    'amount_given' => [ "The amount given field is required."],
                ]
            ]);
    }

    public function test_addDonation_return_200(): void
    {
        $this->json('POST', '/api/protected/donations/add_contribute', [
            'donation_id' =>  $this->donation->id,
            'amount_given' => 45000,
        ])->assertStatus(200);
    }

    public function test_getUserDonationsById_return_404():void
    {
        UserDonation::create([
            'donation_id'       => $this->donation->id,
            'amount_given'      => 10000,
            'user_id'           => $this->user->id,
        ]);

        $response = $this->get('api/protected/donations/'.$this->donation->id.'/contributions?per_page=4');

        $response->assertOk();
        $responseData = json_decode($response->content());

        self::assertEquals($responseData->data->data[0]->user->id, $this->user->id);
        self::assertEquals($responseData->data->data[0]->user->email, $this->user->email);
        self::assertEquals($responseData->data->data[0]->user->name, $this->user->name);
        self::assertEquals($responseData->data->data[0]->user->telephone, $this->user->telephone);
        self::assertNotNull($responseData->data->data[0]->donation->title, $this->donation->title);
        self::assertNotNull($responseData->data->data[0]->donation->description, $this->donation->description);
        self::assertNotNull($responseData->data->data[0]->donation->deadline, $this->donation->deadline);
        self::assertEquals(10000, $responseData->data->data[0]->amount_given);
        self::assertNotNull($responseData->data->data[0]->donation->estimated_amount, $this->donation->estimated_amount);
    }
}
