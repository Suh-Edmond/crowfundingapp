<?php

namespace App\interfaces;

interface UserDonationInterface
{
    public function addDonation($request);

    public function getAllUsersDonationsByDonationId($id, $request);
}
