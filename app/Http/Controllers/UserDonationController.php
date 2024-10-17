<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContributeDonationRequest;
use App\Services\UserDonationService;
use App\Traits\ResponseTrait;

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

    public function getUserDonationsById($id)
    {
        $data = $this->userDonationService->getAllUsersDonationsByDonationId($id);

        return $this->sendResponse($data, "success", 200);
    }
}
