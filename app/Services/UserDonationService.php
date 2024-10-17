<?php

namespace App\Services;

use App\Constants\Constant;
use App\Constants\DonationStatus;
use App\Exceptions\BusinessValidationException;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Resources\UserDonationsResource;
use App\interfaces\UserDonationInterface;
use App\Models\Donation;
use App\Models\UserDonation;

class UserDonationService implements UserDonationInterface
{

    public function addDonation($request)
    {

        $user = auth()->user();
        $donation = Donation::find($request->donation_id);

        if(!isset($donation)){
            throw new ResourceNotFoundException(Constant::DONATION_NOT_FOUND);
        }
        if($donation->status == DonationStatus::COMPLETE){
            throw new BusinessValidationException(Constant::DONATION_COMPLETED, 403);
        }
        UserDonation::create([
            'amount_given' => $request->amount_given,
            'user_id'      => $user->id,
            'donation_id'  => $donation->id
        ]);
        $this->updateDonationStatus($donation);
    }

    public function getAllUsersDonationsByDonationId($id)
    {
        $donation = Donation::find($id);
        if(!isset($donation)){
            throw new ResourceNotFoundException(Constant::DONATION_NOT_FOUND, 404);
        }
        $user = auth()->user();
        if($donation->user_id != $user->id){
            throw new BusinessValidationException(Constant::DONATION_DOES_NOT_BELONG_THIS_USER, 403);
        }
        return UserDonationsResource::collection($this->getDonationsById($id));
    }

    private function getDonationsById($id)
    {
        return UserDonation::where('donation_id', $id)->get();
    }

    private function computeTotalAmountGivenByDonation($id)
    {
        return collect($this->getDonationsById($id))->map(function ($ele) {
            return $ele->amount_given;
        })->sum();
    }

    private function updateDonationStatus($donation)
    {
        if($donation->estimated_amount == $this->computeTotalAmountGivenByDonation($donation->id)){
            $donation->update([
                'status' => DonationStatus::COMPLETE
            ]);
        }
    }
}
