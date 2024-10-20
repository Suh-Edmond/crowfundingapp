<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    use ResponseTrait;

    /**
     * Verify user account. Called automatically when the user clicks the verification link
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        $user = User::find($request->route('id'));
        if ($user->hasVerifiedEmail()) {
            return $this->sendResponse( null, "Email already verified", 200);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $this->sendResponse( null, "Email verified successfully", 200);
    }

    /**
     * Resend account verification email
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendVerification(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return $this->sendResponse( null, "Verification email resent", 200);
    }
}
