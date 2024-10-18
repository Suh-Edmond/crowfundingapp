<?php

namespace App\Http\Requests;

use App\Constants\DonationCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateDonationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * dealine must be a valid date and must be after the current date e,g 2024-12-01 19:00:00
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:donations,title',
            'description' => 'required|max:1000',
            'estimated_amount' => 'required|numeric|min:1000',
            'deadline' => 'required|after_or_equal:now',
            'category' => ['required', Rule::in([DonationCategory::REFUGEE, DonationCategory::EVANGELISM, DonationCategory::HUMANITARIAN]),]
        ];
    }
}
