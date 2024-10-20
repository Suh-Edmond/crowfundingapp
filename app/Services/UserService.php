<?php

namespace App\Services;

use App\Exceptions\UnAuthorizedException;
use App\Http\Resources\TokenResource;
use App\interfaces\UserInterface;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class UserService implements UserInterface
{

    public function createAccount($request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'password' => Hash::make($request->password)
        ]);
        event(new Registered($user));
    }

    public function login($request)
    {
        $user = User::where('email', $request->email)->firstOrFail();
        if(!Hash::check($request->password, $user->password)){
            throw new UnAuthorizedException("Unauthorized", 401);
        }
        if(is_null($user->email_verified_at)){
            throw new UnAuthorizedException("Email not verified", 401);
        }
        return new TokenResource($user, $this->generateToken($user));

    }

    public function logout($request)
    {
        $request->user()->tokens()->delete();
    }

    private function generateToken($user)
    {
        return  $user->createToken($user->id.'-ApiAuthToken')->plainTextToken;
    }



}
