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


    public function createDonation(CreateDonationRequest $request)
    {
        $data = $this->donationService->createDonation($request);

        return $this->sendResponse($data, "Donation created successfully", 200);
    }

    public function updateDonation($id, UpdateDonationRequest $request)
    {
        $data = $this->donationService->updateDonation($request, $id);

        return $this->sendResponse($data, "Donation updated successfully", 200);
    }

    public function showDonation($id)
    {
        $data = $this->donationService->showDonation($id);

        return $this->donationService->showDonation($id);
    }


    public function getAllDonations()
    {
        $data = $this->donationService->fetchAllDonations();

        return $this->sendResponse($data, "success", 200);
    }

    public function getUserDonations()
    {
        $data = $this->donationService->getUserDonations();

        return $this->sendResponse($data, "success", 200);
    }
}
