<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Digikraaft\PaystackSubscription\Billable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'is_google_sign',
        'country',
        'picture',
        'used_storage'

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function subscriptionplan()
    {
        return $this->belongsTo(subscriptionplan::class);
    }
     // Referrals made by the user
     public function referrals()
     {
         return $this->hasMany(Referral::class, 'referrer_id');
     }
 
     // Referrals earned by the user
     public function earnedReferrals()
     {
         return $this->hasMany(Referral::class, 'referred_user_id');
     }
}
