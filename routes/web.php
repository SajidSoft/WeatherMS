<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
// Protect the routes by adding 'auth' middleware
Route::get('/dashboard', [WeatherController::class, 'dashboard'])->name('dashboard');
Route::post('/fetch-weather', [WeatherController::class, 'fetchWeather'])->name('fetch.weather');
Route::get('/weather-data', [WeatherController::class, 'weatherDataTable'])->name('weather.data.table');
Route::get('/home', [HomeController::class, 'index'])->name('home');
