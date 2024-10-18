<?php

namespace Services;

use App\Constants\Constant;
use App\Constants\DonationCategory;
use App\Constants\DonationStatus;
use App\Exceptions\UnAuthorizedException;
use App\Http\Requests\CreateDonationRequest;
use App\Http\Requests\UpdateDonationRequest;
use App\Models\Donation;
use App\Models\User;
use App\Services\DonationService;
use Carbon\Carbon;
use Faker\Generator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Mockery\MockInterface;
use Tests\TestCase;

class DonationServiceTest extends TestCase
{
    use DatabaseMigrations;

    private DonationService $donationService;

    private CreateDonationRequest $createDonationRequest;

    private UpdateDonationRequest $updateDonationRequest;
    private Generator $generator;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->donationService = new DonationService();
        $this->createDonationRequest = new CreateDonationRequest([
            'title' => "Support to IDP",
            'description' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ",
            'estimated_amount' =>  30000,
            'deadline' => Carbon::now()->addMonths(3),
            'category' => DonationCategory::EVANGELISM
        ]);


        $this->updateDonationRequest = new UpdateDonationRequest([
            'title' => "Support to IDP Changed",
            'description' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation change",
            'deadline' => Carbon::now()->addMonths(3),
            'category' => DonationCategory::REFUGEE
        ]);

        $this->user =  User::factory([
            'name' => "john doe",
            'email' => "johndoe@gmail.com",
            'password' => Hash::make('password'),
            'telephone' => "678234156",
            'email_verified_at' => Carbon::now()
        ])->create();

        $this->be($this->user);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_createDonation_creates_a_donation():void
    {

        $created = $this->donationService->createDonation($this->createDonationRequest);

        $jsonResponse = json_decode($created->response()->content());

        $this->assertDatabaseCount(Donation::class, 1);
        $this->assertNotNull($jsonResponse->data->id);
        $this->assertNotNull($jsonResponse->data->created_at);
        $this->assertNotNull($jsonResponse->data->updated_at);
        $this->assertDatabaseHas(Donation::class, ['title' => $jsonResponse->data->title]);
        $this->assertDatabaseHas(Donation::class, ['description' => $jsonResponse->data->description]);
        $this->assertDatabaseHas(Donation::class, ['status' => $jsonResponse->data->status]);
        $this->assertDatabaseHas(Donation::class, ['deadline' => $this->createDonationRequest['deadline']]);
        $this->assertDatabaseHas(Donation::class, ['category' => $jsonResponse->data->category]);
        $this->assertDatabaseHas(Donation::class, ['user_id' => $jsonResponse->data->user_id]);
    }

    public function test_updateDonation_returns_not_found_when_donation_not_exist():void
    {
        $id = "32543";
        $mock = $this->mock(Donation::class, function (MockInterface $mock){
            $mock->shouldReceive('firstOrFail')->andReturnNull();
        });

        $mock->firstOrFail();

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage("No query results for model [App\Models\Donation] 32543");

        $this->donationService->updateDonation($this->updateDonationRequest, $id);
    }

    public function test_updateDonation_return_unauthorized_when_donation_not_belong_to_user():void
    {
        $user = User::factory([
            'name' => "peter John",
            'email' => "peterdoe@gmail.com",
            'password' => "password",
            'telephone' => "673490343",
        ])->create();

        $donation = Donation::create([
            'title'             => $this->createDonationRequest['title'],
            'description'       => $this->createDonationRequest['description'],
            'estimated_amount'  => $this->createDonationRequest['estimated_amount'],
            'user_id'           => $user->id,
            'status'            => DonationStatus::INCOMPLETE,
            'deadline'          => $this->createDonationRequest['deadline'],
            'category'          => $this->createDonationRequest['category']
        ]);

        $mock = $this->mock(Donation::class, function (MockInterface $mock) use ($donation){
            $mock->shouldReceive('firstOrFail')->andReturn($donation);
        });

        $mock->firstOrFail();

        $this->expectException(UnAuthorizedException::class);
        $this->expectExceptionMessage(Constant::UNAUTHORIZED_CAN_NOT_DONATE);
        $this->expectExceptionCode(403);

        $this->donationService->updateDonation($this->updateDonationRequest, $donation->id);
    }

    public function test_updateDonation_should_update_donation():void
    {

        $donation = Donation::create([
            'title'             => $this->createDonationRequest['title'],
            'description'       => $this->createDonationRequest['description'],
            'estimated_amount'  => $this->createDonationRequest['estimated_amount'],
            'user_id'           => $this->user->id,
            'status'            => DonationStatus::INCOMPLETE,
            'deadline'          => $this->createDonationRequest['deadline'],
            'category'          => $this->createDonationRequest['category']
        ]);

        $mock = $this->mock(Donation::class, function (MockInterface $mock) use ($donation){
            $mock->shouldReceive('firstOrFail')->andReturn($donation);
        });

        $mock->firstOrFail();

        $data = $this->donationService->updateDonation($this->updateDonationRequest, $donation->id);
        $jsonResponse = json_decode($data->response()->content());
        $this->assertNotNull($jsonResponse->data->id);
        $this->assertEquals($this->updateDonationRequest['title'], $jsonResponse->data->title);
        $this->assertEquals($jsonResponse->data->category, $this->updateDonationRequest['category']);
        $this->assertEquals($jsonResponse->data->description, $this->updateDonationRequest['description']);
        $this->assertNotNull($jsonResponse->data->user);
    }

    public function test_fetchAllDonations_returns_all_donations():void
    {
        $donation =Donation::create([
            'title'             => $this->createDonationRequest['title'],
            'description'       => $this->createDonationRequest['description'],
            'estimated_amount'  => $this->createDonationRequest['estimated_amount'],
            'user_id'           => $this->user->id,
            'status'            => DonationStatus::INCOMPLETE,
            'deadline'          => $this->createDonationRequest['deadline'],
            'category'          => $this->createDonationRequest['category']
        ]);

        $mock = $this->mock(Donation::class, function (MockInterface $mock) use ($donation){
            $mock->shouldReceive('all')->andReturn(collect($donation));
        });

        $mock->all();

        $data = $this->donationService->fetchAllDonations();

        $response = json_decode($data->response()->content());

        $this->assertCount(1, $response->data);
        $this->assertEquals($response->data[0]->title, $donation->title);
        $this->assertEquals($response->data[0]->description, $donation->description);
        $this->assertEquals($response->data[0]->deadline, $donation->deadline);
        $this->assertEquals($response->data[0]->category, $donation->category);
        $this->assertEquals($response->data[0]->user_id, $donation->user_id);
        $this->assertEquals($response->data[0]->status, $donation->status);
        $this->assertEquals($response->data[0]->user->name, $donation->user->name);
        $this->assertEquals($response->data[0]->user->email, $donation->user->email);
    }

    public function test_showDonation_returns_not_found_when_donation_not_exist():void {
        $id = "21313123";
        $mock = $this->mock(Donation::class, function (MockInterface $mock){
            $mock->shouldReceive('firstOrFail')->andReturnNull();
        });

        $mock->firstOrFail();

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage("No query results for model [App\Models\Donation] 21313123");

        $this->donationService->showDonation($id);
    }


    public function test_showDonation_returns_when_donation_when_exist():void {
        $donation =Donation::create([
            'title'             => $this->createDonationRequest['title'],
            'description'       => $this->createDonationRequest['description'],
            'estimated_amount'  => $this->createDonationRequest['estimated_amount'],
            'user_id'           => $this->user->id,
            'status'            => DonationStatus::INCOMPLETE,
            'deadline'          => $this->createDonationRequest['deadline'],
            'category'          => $this->createDonationRequest['category']
        ]);

        $mock = $this->mock(Donation::class, function (MockInterface $mock) use ($donation){
            $mock->shouldReceive('firstOrFail')->andReturn($donation);
        });

        $mock->firstOrFail();

        $data = $this->donationService->showDonation($donation->id);
        $response = json_decode($data->response()->content());

        $this->assertEquals($donation->title, $response->data->title);
        $this->assertEquals($response->data->description, $donation->description);
        $this->assertEquals($response->data->deadline, $donation->deadline);
        $this->assertEquals($response->data->category, $donation->category);
        $this->assertEquals($response->data->user_id, $donation->user_id);
        $this->assertEquals($response->data->status, $donation->status);
        $this->assertEquals($response->data->user->name, $donation->user->name);
        $this->assertEquals($response->data->user->email, $donation->user->email);
        $this->assertNotNull($response->data->id);
        $this->assertNotNull($response->data->created_at);
    }


    public function test_getUserDonations_returns_all_user_donations():void
    {
        $donation =Donation::create([
            'title'             => $this->createDonationRequest['title'],
            'description'       => $this->createDonationRequest['description'],
            'estimated_amount'  => $this->createDonationRequest['estimated_amount'],
            'user_id'           => $this->user->id,
            'status'            => DonationStatus::INCOMPLETE,
            'deadline'          => $this->createDonationRequest['deadline'],
            'category'          => $this->createDonationRequest['category']
        ]);

        $mock = $this->mock(User::class, function (MockInterface $mock) use ($donation){
            $mock->shouldReceive('donations')->andReturn($donation);
        });

        $mock->donations();

        $data = $this->donationService->getUserDonations();

        $response = json_decode($data->response()->content());

        $this->assertCount(1, $response->data);
        $this->assertEquals($response->data[0]->title, $donation->title);
        $this->assertEquals($response->data[0]->description, $donation->description);
        $this->assertEquals($response->data[0]->deadline, $donation->deadline);
        $this->assertEquals($response->data[0]->category, $donation->category);
        $this->assertEquals($response->data[0]->user_id, $donation->user_id);
        $this->assertEquals($response->data[0]->status, $donation->status);
        $this->assertEquals($response->data[0]->user->name, $donation->user->name);
        $this->assertEquals($response->data[0]->user->email, $donation->user->email);
    }
}

