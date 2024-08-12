<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TicketsController;
use App\Http\Controllers\CategoriesController;

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


//?start//
// ! all routes / api here must be authentcated
Route::group(['middleware' => ['api']], function () {


    //?start//
    // todo group user to login & logout & register //
    Route::group(['prefix' =>'users'], function () {
      
        Route::POST('/login', [UserController::class, 'login']);
        Route::POST('/regist',[UserController::class, 'register']);
        
        // todo return image users //
        Route::get('/imageusers/{avatar}',[UserController::class, 'imagesuser']);

        Route::POST('/logout',[UserController::class, 'logout'])->middleware('auth.guard:api');
        //// ? return profile information ////
        Route::get('/profile',[UserController::class, 'profile'])->middleware('auth.guard:api');
        //// ? todo change image of user ////
        Route::POST('/change-img',[UserController::class, 'changeimg'])->middleware('auth.guard:api');

    });
    //?end//


    //?start//
    // todo group categories //
    Route::group(['prefix' =>'cat'], function () {
     
        Route::GET('/retrieve/categories', [CategoriesController::class, 'index'])->middleware('auth.guard:api');;
        // todo return info of cat //
        Route::get('/Show-cat',[CategoriesController::class, 'show']);
        // todo return image cat //
        Route::get('/imagecat/{cat}',[CategoriesController::class, 'imagescat']);


    });
    //?end//

    //?start//
    // todo group Tickets  //
    Route::group(['middleware' => ['auth.guard:api']], function () {
    Route::group(['prefix' =>'ticket'], function () {
        
        Route::GET('/retrieve/tickets', [TicketsController::class, 'index']);
        Route::POST('/new/ticket', [TicketsController::class, 'store']);
        Route::GET('/edit/ticket', [TicketsController::class, 'edit']);
        Route::PUT('/update/ticket', [TicketsController::class, 'update']);
        Route::DELETE('/soft-deleted/ticket', [TicketsController::class, 'destroy']);
        Route::GET('/filtering/tickets', [TicketsController::class, 'filter']);
        Route::GET('/filtering/tickets/trash', [TicketsController::class, 'filterTrash']);
        Route::GET('/retrieve/tickets/trashed', [TicketsController::class, 'restoreindex']);
        Route::POST('/restore/tickets', [TicketsController::class, 'restore']);
        Route::GET('/auto/complete/search', [TicketsController::class, 'autocolmpletesearch']);

    });
    });
    //?end//


    
});
//?end//