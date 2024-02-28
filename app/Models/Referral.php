<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_id','referral_code', 'referred_user_id', 'status'
    ];

    // Referrer (User) who made the referral
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    // Referred user (User) associated with the referral (if applicable)
    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }
}
