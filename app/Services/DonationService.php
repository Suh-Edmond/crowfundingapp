<?php

namespace App\Services;

use App\Constants\Constant;
use App\Exceptions\UnAuthorizedException;
use App\Http\Resources\DonationResource;
use App\interfaces\DonationInterface;
use App\Models\Donation;

class DonationService implements DonationInterface
{

    public function createDonation($request)
    {
        $user = auth()->user();
        $created = Donation::create([
            'title'             => $request->title,
            'description'       => $request->description,
            'estimated_amount'  => $request->estimated_amount,
            'user_id'           => $user->id,
            'status'            => Constant::INCOMPLETE,
            'deadline'          => $request->deadline
        ]);

        return new DonationResource($created);
    }

    public function updateDonation($request, $id)
    {
        $donation = Donation::findOrFail($id);
        $user = auth()->user();
        if ($donation->user->id != $user->id){
            throw new UnAuthorizedException("Unauthorized! Cannot update this donation", 403);
        }
        $donation->update([
            'title'         => $request->title,
            'description'   => $request->description,
            'deadline'      => $request->deadline
        ]);

        return new DonationResource($donation);
    }

    public function fetchAllDonations()
    {
        return DonationResource::collection(Donation::all());
    }

    public function showDonation($id)
    {
        $donation = Donation::findOrFail($id);

        return new DonationResource($donation);
    }

    public function getUserDonations()
    {
        $user = auth()->user();

        return DonationResource::collection($user->donations);
    }
}
