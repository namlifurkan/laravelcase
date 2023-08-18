<?php

use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\SchoolController;
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

Route::any('/submit', [OfferController::class, 'submitForm'])->name('submit-form');

Route::get('/schools', [SchoolController::class, "index"])->name('school');
Route::get('/schools/{school_id}', ['as' => 'school.show', SchoolController::class, "show"])->name('school.show');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('offers/', [OfferController::class, 'list'])->name('offers');
    Route::get('offers/{offer_id}/approve', [OfferController::class, 'approveOffer'])->name('approveOffer');
    Route::get('offers/{offer_id}/reject', [OfferController::class, 'rejectOffer'])->name('rejectOffer');
});
