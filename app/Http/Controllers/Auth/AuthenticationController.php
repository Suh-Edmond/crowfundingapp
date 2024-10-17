<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\UserService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    private UserService $userService;
    use ResponseTrait;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function createAccount(RegisterRequest $request)
    {
        $this->userService->createAccount($request);

        return $this->sendResponse(null,  "Account created Successfully", 204);
    }

    public function loginUser(LoginRequest $request)
    {
        $data = $this->userService->login($request);

        return $this->sendResponse($data, "Login was successful", 200);
    }

    public function logout(Request $request)
    {
        $this->userService->logout($request);

        return $this->sendResponse(null, "Logout was successful", 204);
    }

}
