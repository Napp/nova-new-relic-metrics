<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Napp\NewRelicMetrics\NewRelic;

/*
|--------------------------------------------------------------------------
| Card API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your card. These routes
| are loaded by the ServiceProvider of your card. You're free to add
| as many additional routes to this file as your card may require.
|
*/

Route::get('/transactions', function (Request $request) {
    return response()->json((new NewRelic())->transactions());
});
