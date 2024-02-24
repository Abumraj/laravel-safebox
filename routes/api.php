<?php

use App\Http\Api\Controllers\Auth\UserController as AuthUserController;
use App\Http\Controllers\Api\Auth\UserController;
use App\Http\Controllers\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [UserController::class, 'register']);
Route::post('google', [UserController::class, 'googleLogin']);
Route::post('login', [UserController::class, 'login']);


// Route::controller(UserController::class)
//     ->group(function () {
    //         Route::post('/register', 'register');
    //     });
    Route::controller(\App\Http\Controllers\Api\Auth\FileController::class)
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::post('/contact/store', [Contact::class, 'saveContacts']);
        Route::get('/contacts', [Contact::class, 'index']);
        Route::get('/contacts/restore', [Contact::class, 'restoreContacts']);
        Route::post('update-password', [UserController::class, 'updatePassword']);
        Route::post('update-profile', [UserController::class, 'updateProfile']);
        Route::get('logout', [UserController::class, 'logout']);
        Route::get('my-detail', [UserController::class, 'myDetail']);
        Route::get('plans', [UserController::class, 'plans']);
        Route::get('products', [UserController::class, 'products']);
        Route::post('upload', [UserController::class, 'upload']);
        Route::get('/my-files/{folder?}', 'myFiles')
            ->where('folder', '(.*)');
        Route::get('/trash', 'trash');
        Route::get('/recent-files', 'recentFiles');
        Route::get('/files-by-type/{id}', 'filesByType');
        Route::post('/folder/create', 'createFolder');
        Route::post('/folder/rename', 'renameFolder');
        Route::post('/file', 'store');
        Route::delete('/file', 'destroy');
        Route::post('/file/restore', 'restore');
        Route::delete('/file/delete-forever', 'deleteForever');
        Route::post('/file/add-to-favourites', 'addToFavourites');
        Route::get('/file/download', 'download');
    });
