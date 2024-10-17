<?php

namespace App\Models;

use App\Traits\GenerateUUIDTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    use GenerateUUIDTrait;

    protected $primaryKey = 'id';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'title',
        'description',
        'estimated_amount',
        'deadline',
        'user_id',
        'status',
        'category'
    ];

    protected $with = [
        'user',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userDonations()
    {
        return $this->hasMany(UserDonation::class);
    }
}
