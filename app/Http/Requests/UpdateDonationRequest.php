<?php

namespace App\Http\Requests;

use App\Constants\DonationCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDonationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|max:1000',
            'deadline' => 'required|after_or_equal:now',
            'category' => ['required', Rule::in([DonationCategory::REFUGEE, DonationCategory::EVANGELISM, DonationCategory::HUMANITARIAN]),]
        ];
    }
}
