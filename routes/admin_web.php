<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any}', function () {
    return view('welcome-vue-admin');
})->where('any', '.*');