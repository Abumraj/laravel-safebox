<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Events\UserRegistered;
use App\Models\subscriptionplan;
use App\Models\User;
use App\Models\Referral;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ReferralResource;
use App\Http\Resources\SubscriptionplanResource;
use App\Http\Resources\UserResource;
use App\Models\Product;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function register(Request $request)
    {
        $response = collect();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:'.User::class,
            'phone_number' => 'required|string|max:255',
            'password' => ['required', Rules\Password::defaults()],
        ]);
        // if(!$request->validate())
        // {
        //     return "email already exist.";
        // }
        $subscriptionId = subscriptionplan::where('name','free')->pluck('id');
        //  return $subscriptionId[0];
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'country' => $request->country,
            'password' => Hash::make($request->password),
        ]);
        event(new UserRegistered($user));
        if ($request->referral_code != null) {
            $referingUser = User::where('referral_code', $request->referral_code)->first();
            Referral::create(
                [
                    'referrer_id' => $referingUser->id,
                    'referral_code' => $request->referral_code,
                    'referred_user_id' => $user->id,
                ]
            );
        }

        Auth::login($user);
        $token = $user->createToken($request->email)->plainTextToken;
        $response->put('token', $token);
        $sub = User::find($user->id);
        $sub->subscriptionplan_id = $subscriptionId[0];
        $sub->save();
        $file = new File();
        $file->name = $user->email;
        $file->is_folder = 1;
        $file->makeRoot()->save();
        $response->put('message', 'you have successfully registered');
        return $response;
    }


  public function googleLogin(Request $request)
  
  {
    $response = collect();
    $name = $request->name;
    $email = $request->email;

    $isUserGoogleRegistered = User::where('email', $email)->where('is_google_sign', 1)->first();
    $user = User::where('email', $email)->where('is_google_sign', 0)->first();
     if ($isUserGoogleRegistered) {

        $isUserGoogleRegistered->tokens()->delete();
        $token = $isUserGoogleRegistered->createToken($request->email)->plainTextToken;
        $response->put('token', $token);
        $response->put('message', 'login successful');
        $response->put('is_verified', 1);

        return $response;
        # code...
     } else if ($user) {
        $response->put('message', 'You cannot use google login for this email on SafeBox');
        // throw ValidationException::withMessages([
        //     'message' => ['The provided credentials are incorrect.'],
        // ]);
        return $response;    
     }else{
        $subscriptionId = subscriptionplan::where('name','free')->pluck('id');
        //  return $subscriptionId[0];
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
           
        ]);
        event(new UserRegistered($user));

        Auth::login($user);
        $token = $user->createToken($request->email)->plainTextToken;
        $response->put('token', $token);
        $sub = User::find($user->id);
        $sub->subscriptionplan_id = $subscriptionId[0];
        $sub->save();
        $file = new File();
        $file->name = $user->email;
        $file->is_folder = 1;
        $file->makeRoot()->save();
        $response->put('message', 'you have successfully registered');
        return $response;
     }
     
    

  }



    public function login(Request $request)
    {
        $response = collect();

        $is_email_verified = 0;
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            $response->put('message', 'The provided credentials are incorrect');
            // throw ValidationException::withMessages([
            //     'message' => ['The provided credentials are incorrect.'],
            // ]);
            return $response;
        }
    if($user->email_verified_at != null){
        $is_email_verified = 1;
    }
        $user->tokens()->delete();
        $token = $user->createToken($request->email)->plainTextToken;
        $response->put('token', $token);
        $response->put('message', 'login successful');
        $response->put('is_verified', $is_email_verified);

        return $response;
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    // public function store(Request $request): RedirectResponse
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:'.User::class,
    //         'password' => ['required', 'confirmed', Rules\Password::defaults()],
    //     ]);

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //     ]);

    //     event(new Registered($user));

    //     Auth::login($user);

    //     $file = new File();
    //     $file->name = $user->email;
    //     $file->is_folder = 1;
    //     $file->makeRoot()->save();

    //     return redirect(RouteServiceProvider::HOME);
    // }


    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return 'Password changed successfully';
    }

    public function updateProfile(ProfileUpdateRequest $request)
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return 'Profile updated successfully';
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return 'Account deleted';
    }

    public function logout()
    {
        $userId = Auth::user()->id;
        $user = User::find($userId);
        $user->tokens()->delete();
        return ' Logged out successfully';
    }

    public function myDetail()
    {

        $user = User::find(Auth::id());
        return new UserResource($user);
    }

    public function referrals()
    {

        $user = User::find(Auth::id());
        $referralsMadeByUser = $user->referrals;
        return ReferralResource::collection($referrals);
    }

    public function plans()
    {
        $sub =   subscriptionplan::all();
        return SubscriptionplanResource::collection($sub);
    }
    public function products()
    {
        $products =  Product::where('status', 1)->get();
        return ProductResource::collection($products);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'user_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust maximum file size as needed
        ]);

        if ($request->file('user_image')->isValid()) {
            $imageName = time() . '.' . $request->user_image->extension();
            $request->user_image->move(public_path('images'), $imageName); // Store image in the public/images directory

            // You can also store the image path in the database if needed
            // Example: $user->update(['image_path' => $imageName]);

            return  'Image uploaded successfully.';
        }

        return 'Failed to upload image.';
    }
   private function addReferral()
   {

   }

}
