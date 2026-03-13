<?php

use App\Http\Controllers\GoldPriceController;
use Illuminate\Support\Facades\Route;

Route::get('/v1/prices-by-date', [GoldPriceController::class, 'pricesByDate']);
Route::get('/v1/sjc-chart', [GoldPriceController::class, 'sjcChart']);
Route::get('/v1/world-chart', [GoldPriceController::class, 'worldChart']);
Route::get('/v1/brand-chart', [GoldPriceController::class, 'brandChart']);
Route::get('/v1/all-brands-chart', [GoldPriceController::class, 'allBrandsChart']);
Route::get('/v1/price-feed', [GoldPriceController::class, 'priceFeed']);
