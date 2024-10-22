<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
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




Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('check.isActive');



Route::middleware('auth')->group(function () {

    Route::get('profile', [UserController::class, 'show'])->name('profile');
    Route::post('/profile/password', [UserController::class, 'updateMyPassword'])
        ->name('profile.password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Category
    Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/getCategoryById/{id}', [CategoryController::class, 'show'])->name('categories.show');
    Route::post('/createCategory', [CategoryController::class, 'store'])
        ->middleware('check.isAdmin')
        ->name('categories.store');

    Route::post('/updateCategory', [CategoryController::class, 'update'])
        ->middleware('check.isAdmin')
        ->name('categories.edit');

    Route::delete('/deleteCategory/{id}', [CategoryController::class, 'destroy'])
        ->middleware('check.isAdmin')
        ->name('categories.destroy');



    // File
    Route::post('/files/upload', [FileController::class, 'upload'])
        ->middleware('check.isAdmin')
        ->name('file.upload');

    Route::delete('/files/{id}', [FileController::class, 'delete'])
        ->middleware('check.isAdmin')
        ->name('file.delete');

    Route::get('/files/display/{id}', [FileController::class, 'display'])->name('file.display');
    Route::get('/files/download/{id}', [FileController::class, 'download'])->name('file.download');




    // User
    Route::get('users', [UserController::class, 'index'])
        ->middleware('check.isAdmin')
        ->name('users');


    Route::post('/users', [UserController::class, 'store'])
        ->middleware('check.isAdmin')
        ->name('users.store');


    Route::get('/users/{id}/toggle-active', [UserController::class, 'toggleActive'])
        ->middleware('check.isAdmin')
        ->name('users.toggleActive');

    Route::post('/users/{id}/change-password', [UserController::class, 'changePassword'])
        ->middleware('check.isAdmin')
        ->name('users.changePassword');
});
