<?php

use A17\Twill\Facades\TwillRoutes;
use App\Http\Controllers\Twill\UnitController;
use App\Http\Controllers\Twill\UserController;

// Register Twill routes here eg.
// TwillRoutes::module('posts');
TwillRoutes::module('platformSettings');
TwillRoutes::module('companies');
TwillRoutes::module('departments');
TwillRoutes::module('jobRoles');
TwillRoutes::module('units');



Route::get('/getDepartments/{id}', [UnitController::class, 'getDepartments']);

Route::get('userUpload', [UserController::class, 'uploadForm'])->name('uploadForm');
Route::post('/userUpload', [UserController::class, 'uploadStore'])->name('uploadUsers');
Route::get('/check-export-status', [UserController::class, 'checkStatus'])->name('uploadUsers.status');
Route::get('/download-uploadUsers/{file}', [UserController::class, 'download'])->name('uploadUsers.download');

