<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/registration', function () {
    return view('registration');
});

Route::get('/signin', function () {
    return view('signin');
});
