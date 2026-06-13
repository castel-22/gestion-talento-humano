<?php

use App\Http\Controllers\EmployeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Ruta autenticada para obtener el usuario actual (Breeze/Sanctum)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// NOTA: Las rutas de empleados fueron movidas a routes/web.php para permitir
// la autenticación basada en sesión/cookies nativa de la web (evitando errores 401 en AJAX).