<?php

use App\Livewire\Auth\LoginForm;
use App\Livewire\Auth\CharacterRegistration;
use App\Livewire\World;
use Illuminate\Support\Facades\Route;

Route::get('/login', LoginForm::class)->name('login');
Route::get('/', World::class)->name('livewire.world');
Route::get('/register', CharacterRegistration::class);

