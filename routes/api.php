<?php

declare(strict_types=1);
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Trip Sathi API — /api/v1
|--------------------------------------------------------------------------
|
| Public catalogue endpoints are cacheable and read-only. Anything that reads
| or writes account/booking state lives behind Sanctum (`auth:sanctum`) so the
| same Bearer token works for both a browser SPA and a native client.
|
*/

Route::prefix('v1')->group(function () {
    Route::get('/health', function (): JsonResponse {
        return response()->json([
            'ok' => true,
            'service' => 'tripsathi-api',
            'version' => 'v1',
            'time' => now()->toIso8601String(),
        ]);
    });
});
