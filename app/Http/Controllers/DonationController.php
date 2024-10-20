<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDonationRequest;
use App\Http\Requests\UpdateDonationRequest;
use App\Services\DonationService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    use ResponseTrait;

    private DonationService $donationService;

    public function __construct(DonationService $donationService)
    {
        $this->donationService = $donationService;
    }

    /**
     *  Create a community donation
     * @param CreateDonationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createDonation(CreateDonationRequest $request)
    {
        $data = $this->donationService->createDonation($request);

        return $this->sendResponse($data, "Donation created successfully", 200);
    }

    /**
     * Update a created community donation
     * @param $id
     * @param UpdateDonationRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\UnAuthorizedException
     */
    public function updateDonation($id, UpdateDonationRequest $request)
    {
        $data = $this->donationService->updateDonation($request, $id);

        return $this->sendResponse($data, "Donation updated successfully", 200);
    }

    /**
     * Show a community donation
     * @param $id
     * @return \App\Http\Resources\DonationResource
     */
    public function showDonation($id)
    {
        $data = $this->donationService->showDonation($id);

        return $this->donationService->showDonation($id);
    }


    /**
     *  Fetch all community donations
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllDonations(Request $request)
    {
        $data = $this->donationService->fetchAllDonations($request);

        return $this->sendResponse($data, "success", 200);
    }

    /**
     * Fetch all donations belonging to the current authenticated user.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserDonations()
    {
        $data = $this->donationService->getUserDonations();

        return $this->sendResponse($data, "success", 200);
    }
}
