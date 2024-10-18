<?php

namespace App\interfaces;

interface DonationInterface
{
    public function createDonation($request);

    public function updateDonation($request, $id);

    public function fetchAllDonations($request);

    public function showDonation($id);

    public function getUserDonations();


}
