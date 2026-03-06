<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

// Register CMS routes
Route::cms();

