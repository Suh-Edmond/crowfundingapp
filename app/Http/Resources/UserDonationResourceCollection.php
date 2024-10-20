<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserDonationResourceCollection extends ResourceCollection
{
    private $total_amount;
    private $total;
    private $lastPage;
    private $perPage;
    private $currentPage;

    public function __construct($collection, $total, $currentPage, $lastPage, $perPage, $total_amount)
    {
        parent::__construct($collection);
        $this->total_amount = $total_amount;
        $this->total = $total;
        $this->lastPage = $lastPage;
        $this->perPage = $perPage;
        $this->currentPage = $currentPage;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return  [
        'data'           =>$this->collection,
        'total_amount'   => $this->total_amount,
        'total'          => $this->total,
        'last_page'      => $this->lastPage,
        'per_page'       => $this->perPage,
        'current_page'   => $this->currentPage
    ];
    }
}
