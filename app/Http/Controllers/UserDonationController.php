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


    /**
     * Add a user contribution(s) to a community donation
     *
     * @param ContributeDonationRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessValidationException
     * @throws \App\Exceptions\ResourceNotFoundException
     */
    public function addDonation(ContributeDonationRequest $request)
    {

        /**
         * donation_id is a valid uuid
         */
        $this->userDonationService->addDonation($request);

        return $this->sendResponse(null, "Your Donation was recorded successfully", 204);
    }

    /**
     * Fetch all contributions provided to a particular donation. Only the owner of the donation can see the contributions
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\BusinessValidationException
     * @throws \App\Exceptions\ResourceNotFoundException
     */
    public function getUserDonationsById($id, Request $request)
    {
        $data = $this->userDonationService->getAllUsersDonationsByDonationId($id, $request);

        return $this->sendResponse($data, "success", 200);
    }
}
