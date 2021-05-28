<?php

    use App\Http\Controllers\Auth\LoginController;
    use App\Http\Controllers\Auth\RegisterController;
    use App\Http\Controllers\Auth\AuthController;
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

    Route::group(['prefix' => 'v1' ], function () {
        Route::group(['middleware'=> 'throttle:60,1', 'prefix'=> 'auth'], function (){
            Route::get('login', [LoginController::class, 'getLogin'])->name('login');
            Route::post('login', [LoginController::class, 'login']);
            Route::post('user/register', [RegisterController::class, 'register']);
            Route::post('hospital/register', [RegisterController::class, 'hospitalRegister']);
            Route::group(['prefix'=>'user'], function (){
                Route::group(['middleware'=>['auth:api','jwt.verify']], function () {

                    Route::get('profile', [AuthController::class, 'profile']);

                    Route::post('logout', [AuthController::class, 'logout']);
                });
            });
            Route::group(['prefix'=>'hospital'], function (){
                Route::group(['middleware'=>['auth:hospital','jwt.verify']], function () {

                    Route::get('profile', [AuthController::class, 'profile']);

                    Route::post('logout', [AuthController::class, 'logout']);
                });
            });
        });
        Route::group(['prefix'=>'user', 'middleware'=>['auth:api','jwt.verify']], function (){
            Route::get('categories', [\App\Http\Controllers\User\AppController::class, 'categories']);
            Route::get('categories/{category}/hospitals', [\App\Http\Controllers\User\AppController::class, 'hospitals']);
            Route::get('categories/{category}/hospitals/{hospital}/beds',
                [\App\Http\Controllers\User\AppController::class, 'beds']);
            Route::post('beds/{bed}',
                [\App\Http\Controllers\User\AppController::class, 'reserve']);
            Route::get('reservations/upcoming', [\App\Http\Controllers\User\AppController::class, 'upcoming']);
            Route::get('reservations/history', [\App\Http\Controllers\User\AppController::class, 'history']);
        });
        Route::group(['prefix'=>'hospital', 'middleware'=>['auth:hospital','jwt.verify']], function (){
            Route::get('categories', [\App\Http\Controllers\User\AppController::class, 'categories']);
            Route::get('categories/{category}/beds', [\App\Http\Controllers\Hospital\HospitalController::class, 'beds']);
            Route::post('categories/{category}/beds/new', [\App\Http\Controllers\Hospital\HospitalController::class, 'createBed']);
            Route::group(['prefix'=>'beds'], function (){
                Route::get('{bed}/reservations',
                    [\App\Http\Controllers\Hospital\HospitalController::class, 'reservations']);

                Route::post('{bed}/edit',
                    [\App\Http\Controllers\Hospital\HospitalController::class, 'editBed']);
                Route::post('{bed}/delete',
                    [\App\Http\Controllers\Hospital\HospitalController::class, 'deleteBed']);
                Route::post('{bed}/reservations/{reservation}/end',
                    [\App\Http\Controllers\Hospital\HospitalController::class, 'endReservation']);
            });
        });

    });

