<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GenerateReferralCode
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event)
    {
        $referralCode = substr(md5($event->user->email), 0, 8); // Generate referral code
        $event->user->referral_code = $referralCode; // Update user's referral code
        $event->user->save(); // Save the user record
    }
}
