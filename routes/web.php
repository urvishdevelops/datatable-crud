<?php

use App\Http\Controllers\LibraryController;
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

Route::get('/',[LibraryController::class, 'index'])->name('library.index');
Route::post('/libraryView', [LibraryController::class, 'libraryView'])-> name('library.libraryView');
Route::get('/listing', [LibraryController::class, 'listing'])-> name('library.listing');
Route::post('/edit', [LibraryController::class, 'libraryView'])-> name('library.edit');
Route::post('/delete', [LibraryController::class, 'libraryView'])-> name('library.delete');
Route::post('library/upload', [LibraryController::class, 'upload'])->name('dropzone.upload');