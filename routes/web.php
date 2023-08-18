<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/setup', function () {
    $credentials = [
        'email' => 'furkan@admin.com',
        'password' => 'password'
    ];

    if (!auth()->attempt($credentials)) {
        $user = new \App\Models\User();

        $user->name = 'Admin';
        $user->email = $credentials['email'];
        $user->password = bcrypt($credentials['password']);

        $user->save();

        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            $adminToken = $user->createToken('admin-token', ['create', 'update', 'delete']);
            $updateToken = $user->createToken('update-token', ['create', 'update']);
            $basicToken = $user->createToken('basic-token');

            return [
                'admin' => $adminToken->plainTextToken,
                'update' => $updateToken->plainTextToken,
                'basic' => $basicToken->plainTextToken,
            ];
        }
    }
});
