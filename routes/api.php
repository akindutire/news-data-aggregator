<?php

use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;

Route::get('news/categories', [NewsController::class, 'allCategories']);

Route::get('news', [NewsController::class, 'get']);
