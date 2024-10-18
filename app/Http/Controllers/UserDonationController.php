<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContributeDonationRequest;
use App\Services\UserDonationService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class UserDonationController extends Controller
{
    use ResponseTrait;

    private UserDonationService $userDonationService;

    public function __construct(UserDonationService $userDonationService)
    {
        $this->userDonationService = $userDonationService;
    }


    public function addDonation(ContributeDonationRequest $request)
    {

        $this->userDonationService->addDonation($request);

        return $this->sendResponse(null, "Your Donation was recorded successfully", 204);
    }

    public function getUserDonationsById($id, Request $request)
    {
        $data = $this->userDonationService->getAllUsersDonationsByDonationId($id, $request);

        return $this->sendResponse($data, "success", 200);
    }
}
