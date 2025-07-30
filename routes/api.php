<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Services\DynamicRouteService;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Register dynamic routes from database (only if table exists)
try {
    app(DynamicRouteService::class)->registerRoutes();
} catch (\Exception $e) {
    // Table might not exist during migration, skip dynamic routes
}

