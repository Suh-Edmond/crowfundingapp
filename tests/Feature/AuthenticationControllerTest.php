<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use Tests\TestCase;

class AuthenticationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_user_account_return_bad_request(): void
    {
        $response = $this->post('api/public/auth/create_account',['Accept' => "application/json", 'Content-Type'=>'application/json'], [
            'name' =>  '',
            'email' => '',
            'telephone' => '678345167',
            'password' => 'password'
        ]);
        $response->assertSessionHasErrorsIn('name');
        $response->assertSessionHasErrorsIn('email');
        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_createAccount_should_create_account():void
    {
        $userData = [
            'name' =>  'John Doe',
            'email' => 'johndoe@gmail.com',
            'telephone' => '678345167',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];
        $this->json('POST', 'api/public/auth/create_account', $userData, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                "message",
                "success",
                "data",
                "code",
            ]);
    }

    public function test_login_returns_bad_request():void
    {
        $loginData = [
            'email' => "",
            "password" => ""
        ];

        $this->json('POST', 'api/public/auth/login', $loginData, ['Accept' => 'application/json', 'Content-Type' => 'application/json'])
                ->assertStatus(422)
                ->assertJson([
                    'message' => "The email field is required. (and 1 more error)",
                    'errors' =>[
                        'email' => [ "The email field is required."],
                        'password' => ["The password field is required."]
                    ]
                ]);
    }

    public function test_login_returns_200():void
    {
        $user = User::factory([
            'email' => "johndoe@gmail.com",
            "name" => "John Doe",
            "telephone" => "64567456",
            "password" => Hash::make("password")
        ])->create();

        $loginData = [
            'email' => "johndoe@gmail.com",
            "password" => "password"
        ];


        $response = $this->post('api/public/auth/login',$loginData);

        $response->assertOk();
        $responseData = json_decode($response->content());

        self::assertEquals($responseData->data->user->id, $user->id);
        self::assertEquals($responseData->data->user->email, $user->email);
        self::assertEquals($responseData->data->user->name, $user->name);
        self::assertEquals($responseData->data->user->telephone, $user->telephone);
        self::assertNotNull($responseData->data->token);
        self::assertTrue($responseData->success);


    }

    public function test_logout_returns_200():void {
        $user = User::factory([
            'email' => "johndoe@gmail.com",
            "name" => "John Doe",
            "telephone" => "64567456",
            "password" => Hash::make("password")
        ])->create();

        $this->be($user);

        $response = $this->post('api/protected/auth/logout', (array)new Request());

        $response->assertOk();
    }
}
