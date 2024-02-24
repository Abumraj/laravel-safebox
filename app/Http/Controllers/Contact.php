<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactStoredRequest;
use App\Http\Resources\ContactResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Contact extends Controller
{
    public function index()

    {
 // Get the authenticated user
      $userId = Auth::id();
     $user = User::findOrFail($userId);
 // Paginate the contacts with a specified number per page
     $contacts = $user->contacts()->paginate(20);

    return ContactResource::collection($contacts);

    }


public function restoreContacts()

{

    // Get the authenticated user
    $userId = Auth::id();
    $user = User::findOrFail($userId);
// Paginate the contacts with a specified number per page
    $contacts = $user->contacts()->paginate(500);

   return ContactResource::collection($contacts);


}



    public function saveContacts(ContactStoredRequest $request)
{

    $result = collect();

    $userId = Auth::id();
     // Extract validated contacts from the request
     $contacts = $request->validated()['contacts'];

     // Use the insert method to store multiple contacts at once
   // Find the user by ID
   $user = User::findOrFail($userId);
   // Use the relationship to save contacts for the user
   $user->contacts()->createMany($contacts);
   $message = 'Contacts uploaded Successfully';
   $result->put('message', $message);

   return $result;

}
}
