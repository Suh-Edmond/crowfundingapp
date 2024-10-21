<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class VerificationControllerTest extends TestCase
{
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

    use RefreshDatabase;



    public function test_resendVerification_should_resend_email(): void
    {
        $response = $this->post('/api/protected/email/verification-notification');

        $response->assertStatus(200);
    }

}
