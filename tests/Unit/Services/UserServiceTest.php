<?php

namespace Services;

use App\Exceptions\UnAuthorizedException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mockery\MockInterface;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class UserServiceTest extends TestCase
{
    use DatabaseMigrations;
    private UserService $userService;
    private RegisterRequest $request;

    private LoginRequest $loginRequest;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userService = new UserService();

        $this->request = new RegisterRequest([
            'name' => "John Doe",
            'email' => "johndoe@gmail.com",
            'password' => "example_password",
            'telephone' => '678334832',
            'password_confirmation' => 'example_password'
        ]);

        $this->loginRequest = new LoginRequest([
            'email' => 'johndoe@gmail.com',
            'password' => 'example_password'
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }


    public function test_createAccount_created_an_account():void
    {
        $this->userService->createAccount($this->request);

        $this->assertDatabaseCount(User::class, 1);
        $this->assertDatabaseHas(User::class, ['name' => $this->request['name']]);
        $this->assertDatabaseHas(User::class, ['email' => $this->request['email']]);
        $this->assertDatabaseHas(User::class, ['telephone' => $this->request['telephone']]);
    }

    public function test_login_returns_unauthorized_when_user_not_found():void
    {
        $mock = $this->mock(User::class, function (MockInterface $mock) {
            $mock->shouldReceive('firstOrFail')->once()->andReturnNull();
        });

        $mock->firstOrFail();
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage("No query results for model [App\Models\User].");

        $this->userService->login($this->loginRequest);
    }

    public function test_login_return_unauthorized_when_password_is_invalid():void
    {
        $user = User::factory([
            'name' => $this->request['name'],
            'email' => $this->request['email'],
            'password' => "wrong_password",
            'telephone' => $this->request['telephone'],
        ])->create();


        $mock = $this->mock(User::class, function (MockInterface $mock) use ($user){
            $mock->shouldReceive('firstOrFail')->once()->andReturn($user);
        });

        $mock->firstOrFail();

        $this->expectException(UnAuthorizedException::class);
        $this->expectExceptionMessage("Unauthorized");
        $this->expectExceptionCode(401);

        $this->userService->login($this->loginRequest);
    }

    public function test_login_return_unauthorized_when_email_not_verify():void
    {
        $user = User::factory([
            'name' => $this->request['name'],
            'email' => $this->request['email'],
            'password' => Hash::make($this->request['password']),
            'telephone' => $this->request['telephone'],
            'email_verified_at' => null
        ])->create();


        $mock = $this->mock(User::class, function (MockInterface $mock) use ($user){
            $mock->shouldReceive('firstOrFail')->once()->andReturn($user);
        });

        $mock->firstOrFail();

        $this->expectException(UnAuthorizedException::class);
        $this->expectExceptionMessage("Email not verified");
        $this->expectExceptionCode(401);

        $this->userService->login($this->loginRequest);
    }


    public function test_login_return_login_user_when_password_valid_and_email_verified():void
    {
        $user = User::factory([
            'name' => $this->request['name'],
            'email' => $this->request['email'],
            'password' => Hash::make($this->request['password']),
            'telephone' => $this->request['telephone'],
            'email_verified_at' => Carbon::now()
        ])->create();


        $mock = $this->mock(User::class, function (MockInterface $mock) use ($user){
            $mock->shouldReceive('firstOrFail')->once()->andReturn($user);
        });

        $mock->firstOrFail();

        $response = $this->userService->login($this->loginRequest);

        $jsonResponse = json_decode($response->response()->content());

        $this->assertNotNull($jsonResponse->data->token);
        $this->assertEquals($this->request['name'], $jsonResponse->data->user->name);
        $this->assertEquals($this->request['email'], $jsonResponse->data->user->email);
        $this->assertEquals($this->request['telephone'], $jsonResponse->data->user->telephone);
        $this->assertNotNull($jsonResponse->data->user->id);
        $this->assertNotNull($jsonResponse->data->user->created_at);
        $this->assertNotNull($jsonResponse->data->user->updated_at);
    }


}
