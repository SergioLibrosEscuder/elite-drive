<?php

use Illuminate\Support\Facades\Route;
/*
Route::get('/', function () {
    return view('app');
});
*/
// Gemini AI Integration - Single Page Application Route
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
