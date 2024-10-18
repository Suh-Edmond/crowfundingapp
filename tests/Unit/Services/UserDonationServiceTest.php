<?php

namespace Services;

use App\Constants\Constant;
use App\Constants\DonationCategory;
use App\Constants\DonationStatus;
use App\Exceptions\BusinessValidationException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Requests\ContributeDonationRequest;
use App\Models\Donation;
use App\Models\User;
use App\Models\UserDonation;
use App\Services\UserDonationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Mockery\MockInterface;
use Tests\TestCase;


class UserDonationServiceTest extends TestCase
{
    use DatabaseMigrations;

    private UserDonationService $userDonationService;

    private ContributeDonationRequest $request;
    private User $user;
    private Donation $donation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user =  User::factory([
            'name' => "john doe",
            'email' => "johndoe@gmail.com",
            'password' => Hash::make('password'),
            'telephone' => "678234156",
            'email_verified_at' => Carbon::now()
        ])->create();

        $this->be($this->user);


        $this->userDonationService = new UserDonationService();
        $this->request = new ContributeDonationRequest();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_addDonation_returns_not_found_when_donation_not_exist():void
    {
        $mock = $this->mock(Donation::class, function (MockInterface $mock){
            $mock->shouldReceive('find')->andReturnNull();
        });
        $mock->find();

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage(Constant::DONATION_NOT_FOUND);
        $this->expectExceptionCode(404);

        $this->userDonationService->addDonation($this->request);

    }

    public function test_addDonation_returns_validation_when_donation_is_complete():void
    {
        $this->donation = Donation::create([
            'user_id'           => $this->user->id,
            'title' => "Support to IDP",
            'description' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ",
            'estimated_amount' =>  30000,
            'deadline' => "2024-10-17 17:42:00",
            'status' => DonationStatus::COMPLETE,
            'category' => DonationCategory::EVANGELISM
        ]);

        $this->request['donation_id'] = $this->donation->id;
        $this->request['amount_given'] = 45000;

        $mock = $this->mock(Donation::class, function (MockInterface $mock){
            $mock->shouldReceive('find')->andReturn($this->donation);
        });
        $mock->find();

        $this->expectException(BusinessValidationException::class);
        $this->expectExceptionMessage(Constant::DONATION_COMPLETED);
        $this->expectExceptionCode(403);

        $this->userDonationService->addDonation($this->request);

    }


    public function test_addDonation_returns_validation_when_donation_has_expired():void
    {
        $this->donation = Donation::create([
            'user_id'           => $this->user->id,
            'title' => "Support to IDP",
            'description' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ",
            'estimated_amount' =>  30000,
            'deadline' => "2024-10-17 17:42:00",
            'status' => DonationStatus::INCOMPLETE,
            'category' => DonationCategory::EVANGELISM
        ]);

        $this->request['donation_id'] = $this->donation->id;
        $this->request['amount_given'] = 45000;

        $mock = $this->mock(Donation::class, function (MockInterface $mock){
            $mock->shouldReceive('find')->andReturn($this->donation);
        });
        $mock->find();

        $this->expectException(BusinessValidationException::class);
        $this->expectExceptionMessage(Constant::DONATION_HAS_EXPIRED);
        $this->expectExceptionCode(400);

        $this->userDonationService->addDonation($this->request);

    }

    public function test_addDonation_save_donation_and_update_status():void
    {
        $this->donation = Donation::create([
            'user_id'           => $this->user->id,
            'title' => "Support to IDP",
            'description' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ",
            'estimated_amount' =>  100000,
            'deadline' => "2024-12-17 17:42:00",
            'status' => DonationStatus::INCOMPLETE,
            'category' => DonationCategory::EVANGELISM
        ]);

        $this->request['donation_id'] = $this->donation->id;
        $this->request['amount_given'] = 100000;

        $mock = $this->mock(Donation::class, function (MockInterface $mock){
            $mock->shouldReceive('find')->andReturn($this->donation);
        });
        $mock->find();

        $this->userDonationService->addDonation($this->request);

        $this->assertDatabaseCount(UserDonation::class, 1);
        $this->assertDatabaseHas(Donation::class, ['status' => DonationStatus::COMPLETE]);
        $this->assertDatabaseHas(UserDonation::class, ['donation_id' => $this->donation->id]);
        $this->assertDatabaseHas(UserDonation::class, ['amount_given' => $this->request['amount_given']]);
        $this->assertDatabaseHas(UserDonation::class, ['user_id' => $this->user->id]);
    }

    public function test_addDonation_save_donation_and_not_update_status_when_estimated_amount_not_reached():void
    {
        $this->donation = Donation::create([
            'user_id'           => $this->user->id,
            'title' => "Support to IDP",
            'description' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ",
            'estimated_amount' =>  100000,
            'deadline' => "2024-12-17 17:42:00",
            'status' => DonationStatus::INCOMPLETE,
            'category' => DonationCategory::EVANGELISM
        ]);

        $this->request['donation_id'] = $this->donation->id;
        $this->request['amount_given'] = 50000;

        $mock = $this->mock(Donation::class, function (MockInterface $mock){
            $mock->shouldReceive('find')->andReturn($this->donation);
        });
        $mock->find();

        $this->userDonationService->addDonation($this->request);

        $this->assertDatabaseCount(UserDonation::class, 1);
        $this->assertDatabaseHas(Donation::class, ['status' => DonationStatus::INCOMPLETE]);
        $this->assertDatabaseHas(UserDonation::class, ['donation_id' => $this->donation->id]);
        $this->assertDatabaseHas(UserDonation::class, ['amount_given' => $this->request['amount_given']]);
        $this->assertDatabaseHas(UserDonation::class, ['user_id' => $this->user->id]);
    }

    public function test_getAllUsersDonationsByDonationId_returns_not_found_when_user_donation_not_exist():void {
        $id = "3452453";
        $mock = $this->mock(UserDonation::class, function (MockInterface $mock){
            $mock->shouldReceive('find')->andReturnNull();
        });
        $mock->find();

        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionMessage(Constant::DONATION_NOT_FOUND);
        $this->expectExceptionCode(404);
        $request = new Request();
        $this->userDonationService->getAllUsersDonationsByDonationId($id, $request);
    }

    public function test_getAllUsersDonationsByDonationId_returns_unauthorized_when_user_donation_exist_and_does_not_belong_user():void {
        $user = User::factory([
            'name' => "peter John",
            'email' => "peterdoe@gmail.com",
            'password' => "password",
            'telephone' => "673490343",
        ])->create();

        $this->donation = Donation::create([
            'user_id'           => $user->id,
            'title' => "Support to IDP",
            'description' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ",
            'estimated_amount' =>  100000,
            'deadline' => "2024-12-17 17:42:00",
            'status' => DonationStatus::INCOMPLETE,
            'category' => DonationCategory::EVANGELISM
        ]);
        $request = new Request();

        $mock = $this->mock(UserDonation::class, function (MockInterface $mock){
            $mock->shouldReceive('find')->andReturn($this->donation);
        });
        $mock->find();

        $this->expectException(BusinessValidationException::class);
        $this->expectExceptionMessage(Constant::DONATION_DOES_NOT_BELONG_THIS_USER);
        $this->expectExceptionCode(403);

        $this->userDonationService->getAllUsersDonationsByDonationId($this->donation->id, $request);
    }

    public function test_getAllUsersDonationsByDonationId_returns_all_user_donations():void {

        $this->donation = Donation::create([
            'user_id'           => $this->user->id,
            'title'             => "Support to IDP",
            'description'       => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ",
            'estimated_amount'  =>  100000,
            'deadline'          => "2024-12-17 17:42:00",
            'status'            => DonationStatus::INCOMPLETE,
            'category'          => DonationCategory::EVANGELISM
        ]);

        $request = new Request();

        $contribution = UserDonation::create([
            'donation_id' => $this->donation->id,
            'user_id'      => $this->user->id,
            'amount_given' => 45000
        ]);

        $mock = $this->mock(UserDonation::class, function (MockInterface $mock){
            $mock->shouldReceive('find')->andReturn($this->donation);
        });
        $mock->find();

        $data = $this->userDonationService->getAllUsersDonationsByDonationId($this->donation->id, $request);


        self::assertEquals(1, $data->total());
        self::assertEquals($contribution->donation_id, $data->items()[0]->donation_id);
        self::assertEquals($contribution->user_id, $data->items()[0]->user_id);
        self::assertEquals($contribution->amount_given, $data->items()[0]->amount_given);

        self::assertEquals($this->donation->title,$data->items()[0]->donation->title);
        self::assertEquals($this->donation->description, $data->items()[0]->donation->description);
        self::assertEquals($this->donation->deadline, $data->items()[0]->donation->deadline);
        self::assertEquals($this->donation->status, $data->items()[0]->donation->status);
        self::assertEquals($this->donation->category, $data->items()[0]->donation->category);

        self::assertEquals($this->user->name, $data->items()[0]->user->name);
        self::assertEquals($this->user->email, $data->items()[0]->user->email);
        self::assertEquals($this->user->telephone, $data->items()[0]->user->telephone);
    }

}
