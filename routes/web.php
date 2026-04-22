<?php

use App\Http\Controllers\Front\ProfileController;
use App\Http\Controllers\Front\PagesController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PagesController::class, 'index'])->name('home');



Route::middleware('auth')->group(function () {
   
  Route::any('/pages/{key}', [PagesController::class, 'pages'])->name('pages');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
  
});

require __DIR__.'/auth.php';
