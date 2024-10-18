<?php

namespace Tests\Feature;

use App\Constants\DonationCategory;
use App\Constants\DonationStatus;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DonationControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
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

    }




    public function test_createDonation_returns_validation_exception(): void
    {
        $this->json('POST', 'api/protected/user/donations/create',['Accept' => "application/json", 'Content-Type'=>'application/json'], [
            'title' =>  '',
            'description' => '',
            'deadline' => '',
            'estimated_amount' => '0'
        ])->assertStatus(422)
          ->assertJson([
              "message" => "The title field is required. (and 4 more errors)",
              "errors" => [
                  "title" =>["The title field is required."],
                  'description' => ['The description field is required.'],
                  'estimated_amount' => ['The estimated amount field is required.'],
                  'deadline' => ["The deadline field is required."]
              ]
          ]);

    }

    public function test_createDonation_returns_200(): void
    {
        $this->json('POST', 'api/protected/user/donations/create', [
            'title' =>  'my crow fund me',
            'description' => 'helping refugees',
            'deadline' => '2024-12-01 00:00:00',
            'estimated_amount' => '340000',
            'category' => DonationCategory::REFUGEE
        ])->assertStatus(200)->assertJson([
            "success" => true,
            "data"=> [],
            "message" => "Donation created successfully",
            "code" => 200
        ]);
    }

    public function test_updateDonation_returns_422():void
    {
        $donation = Donation::create([
            'title'             => "example title",
            'description'       => "example description",
            'estimated_amount'  => 10000,
            'user_id'           => $this->user->id,
            'status'            => DonationStatus::INCOMPLETE,
            'deadline'          => '2024-12-01 00:00:00',
            'category'          => DonationCategory::REFUGEE
        ]);
        $this->json('PUT', 'api/protected/user/donations/'.$donation->id.'/update',['Accept' => "application/json", 'Content-Type'=>'application/json'], [
            'title' =>  '',
            'description' => '',
            'deadline' => '',
        ])->assertStatus(422)
            ->assertJson([
                "message" => "The title field is required. (and 3 more errors)",
                "errors" => [
                    "title" =>["The title field is required."],
                    'description' => ['The description field is required.'],
                    'deadline' => ["The deadline field is required."],
                    "category"=> ["The category field is required."]
                ]
            ]);
    }

    public function test_updateDonation_returns_200():void
    {
        $donation = Donation::create([
            'title'             => "example title",
            'description'       => "example description",
            'estimated_amount'  => 10000,
            'user_id'           => $this->user->id,
            'status'            => DonationStatus::INCOMPLETE,
            'deadline'          => '2024-12-01 00:00:00',
            'category'          => DonationCategory::REFUGEE
        ]);
        $this->json('PUT', 'api/protected/user/donations/'.$donation->id.'/update', [
            'title' =>  'example title updated',
            'description' => "example description changed",
            'deadline' => '2024-12-01 00:00:00',
            'category' => DonationCategory::REFUGEE
        ])->assertStatus(200)
            ->assertJsonStructure([
                'success'
            ]);
    }

    public function test_showDonation_returns_200():void
    {
        $donation = Donation::create([
            'title'             => "example title",
            'description'       => "example description",
            'estimated_amount'  => 10000,
            'user_id'           => $this->user->id,
            'status'            => DonationStatus::INCOMPLETE,
            'deadline'          => '2024-12-01 00:00:00',
            'category'          => DonationCategory::REFUGEE
        ]);

        $response = $this->get('api/protected/user/donations/'.$donation->id);

        $response->assertOk();
        $responseData = json_decode($response->content());


        self::assertEquals($responseData->data->user->id, $this->user->id);
        self::assertEquals($responseData->data->user->email, $this->user->email);
        self::assertEquals($responseData->data->user->name, $this->user->name);
        self::assertEquals($responseData->data->user->telephone, $this->user->telephone);
        self::assertNotNull($responseData->data->title, $donation->title);
        self::assertNotNull($responseData->data->description, $donation->description);
        self::assertNotNull($responseData->data->deadline, $donation->deadline);
        self::assertNotNull($responseData->data->estimated_amount, $donation->estimated_amount);

    }

    public function test_getAllDonations_returns_200():void
    {
        $donation = Donation::create([
            'title'             => "example title",
            'description'       => "example description",
            'estimated_amount'  => 10000,
            'user_id'           => $this->user->id,
            'status'            => DonationStatus::INCOMPLETE,
            'deadline'          => '2024-12-01 00:00:00',
            'category'          => DonationCategory::REFUGEE
        ]);

        $response = $this->get('api/public/donations?per_page=4');

        $response->assertOk();
        $responseData = json_decode($response->content());

        self::assertEquals($responseData->data->data[0]->user->id, $this->user->id);
        self::assertEquals($responseData->data->data[0]->user->email, $this->user->email);
        self::assertEquals($responseData->data->data[0]->user->name, $this->user->name);
        self::assertEquals($responseData->data->data[0]->user->telephone, $this->user->telephone);
        self::assertNotNull($responseData->data->data[0]->title, $donation->title);
        self::assertNotNull($responseData->data->data[0]->description, $donation->description);
        self::assertNotNull($responseData->data->data[0]->deadline, $donation->deadline);
        self::assertNotNull($responseData->data->data[0]->estimated_amount, $donation->estimated_amount);
    }

    public function test_getUserDonations_returns_200():void
    {
        $donation = Donation::create([
            'title'             => "example title",
            'description'       => "example description",
            'estimated_amount'  => 10000,
            'user_id'           => $this->user->id,
            'status'            => DonationStatus::INCOMPLETE,
            'deadline'          => '2024-12-01 00:00:00',
            'category'          => DonationCategory::REFUGEE
        ]);

        $response = $this->get('api/protected/user/donations');

        $response->assertOk();
        $responseData = json_decode($response->content());

        self::assertEquals($responseData->data[0]->user->id, $this->user->id);
        self::assertEquals($responseData->data[0]->user->email, $this->user->email);
        self::assertEquals($responseData->data[0]->user->name, $this->user->name);
        self::assertEquals($responseData->data[0]->user->telephone, $this->user->telephone);
        self::assertNotNull($responseData->data[0]->title, $donation->title);
        self::assertNotNull($responseData->data[0]->description, $donation->description);
        self::assertNotNull($responseData->data[0]->deadline, $donation->deadline);
        self::assertNotNull($responseData->data[0]->estimated_amount, $donation->estimated_amount);
    }
}
