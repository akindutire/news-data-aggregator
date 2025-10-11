<?php

use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;

Route::get('news/category', [NewsController::class, 'allCategories']);

Route::get('news', [NewsController::class, 'get']);
