<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Sprint 1 authentification / gestion utilisateurs
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {

    Route::post('login', [\App\Http\Controllers\authentification\AuthentificationController::class, 'login']);
    Route::post('register', [\App\Http\Controllers\authentification\AuthentificationController::class, 'register']);
    Route::get('me', [\App\Http\Controllers\authentification\AuthentificationController::class, 'me']);
    Route::post('registerSeller', [\App\Http\Controllers\authentification\AuthentificationController::class, 'registerSeller']);
    Route::post('uploadImage', [\App\Http\Controllers\authentification\AuthentificationController::class, 'uploadImage']);
});

//Route::group(['prefix' => 'Admin',  'middleware' => ['auth:api', 'checkAdmin'] ] ,function ()
Route::group(['prefix' => 'Admin' ] ,function ()
{
    Route::get('sellers', [\App\Http\Controllers\Admin\AdminController::class, 'listSeller']);
    Route::post('seller', [\App\Http\Controllers\Admin\AdminController::class, 'getSellerById']);
    Route::post('VerSeller', [\App\Http\Controllers\Admin\AdminController::class, 'verifSeller']);
    Route::post('FlSeller', [\App\Http\Controllers\Admin\AdminController::class, 'filterSeller']);
    Route::post('getImage', [\App\Http\Controllers\Admin\AdminController::class, 'returnImage']);
});
///////////////////////sprint 2 **********************/
Route::group(['prefix' => 'Seller', 'middleware' => ['auth:api']] ,function ()
{
    Route::post('getImage', [App\Http\Controllers\Sprint2\AnnouncementController\Announcemnet::class, 'returnImage']);
    Route::post('AddAnn', [App\Http\Controllers\Sprint2\AnnouncementController\Announcemnet::class, 'addAnnouce']);
    Route::get('getAnn', [App\Http\Controllers\Sprint2\AnnouncementController\Announcemnet::class, 'getAnnounceProfile']);

});
Route::group(['prefix' => 'auth/User' , 'middleware' => ['auth:api']] ,function ()
{
    Route::get('getAnn', [App\Http\Controllers\Sprint2\AnnouncementController\Announcemnet::class, 'affichPostWithAuth']);
    Route::post('getAnn', [App\Http\Controllers\Sprint2\AnnouncementController\Announcemnet::class, 'affichPostWithAuth']);
});

Route::group(['prefix' => 'User'] ,function ()
{
    Route::get('getAnn', [App\Http\Controllers\Sprint2\AnnouncementController\Announcemnet::class, 'affichPost']);
    Route::post('getAnn', [App\Http\Controllers\Sprint2\AnnouncementController\Announcemnet::class, 'affichPost']);
    Route::post('getAnn2', [App\Http\Controllers\Sprint2\AnnouncementController\Announcemnet::class, 'getAnnounceProfilewithId']);
});
///////////////////////sprint 3 **********************/
Route::group(['prefix' => 'User', 'middleware' => ['auth:api']] ,function ()
{
    Route::post('addLike', [App\Http\Controllers\Sprint3\sprint3Controller::class, 'addLike']);
    Route::post('addBasket', [App\Http\Controllers\Sprint3\sprint3Controller::class, 'addToBasket']);
    Route::post('delBasket', [App\Http\Controllers\Sprint3\sprint3Controller::class, 'removefromBasket']);
    Route::get('getBasket', [App\Http\Controllers\Sprint3\sprint3Controller::class, 'getBasket']);
    Route::post('addstars', [App\Http\Controllers\Sprint3\sprint3Controller::class, 'addstars']);

});

///////////////////////sprint 3 **********************/
Route::group(['prefix' => 'User', 'middleware' => ['auth:api']] ,function ()
{
    Route::post('addSubs', [App\Http\Controllers\Sprint4\sprint4Controller::class, 'addRemoveSubscriber']);
});
Route::group(['prefix' => 'User'] ,function ()
{
    Route::post('getSeller', [App\Http\Controllers\Sprint4\sprint4Controller::class, 'getSeller']);
    Route::post('getInfo', [App\Http\Controllers\Sprint4\sprint4Controller::class, 'getInfo']);
});

