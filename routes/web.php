<?php

use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/client-information',\App\Livewire\ClientInformationForm::class);
