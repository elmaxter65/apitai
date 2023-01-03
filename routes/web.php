<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// proteger rutas solo para usuarios logeados
Route::group(['middleware' => 'auth'], function () {
    Route::get('home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    # modulo de pantallas
    Route::get('pantallas', [App\Http\Controllers\PantallasController::class, 'index']);
    Route::get('pantallas/create', [App\Http\Controllers\PantallasController::class, 'create']);
    Route::post('pantallas/store', [App\Http\Controllers\PantallasController::class, 'store']);
    Route::get('pantallas/edit/{id}', [App\Http\Controllers\PantallasController::class, 'edit']);
    Route::post('pantallas/update', [App\Http\Controllers\PantallasController::class, 'update']);
    Route::get('pantallas/destroy/{id}', [App\Http\Controllers\PantallasController::class, 'destroy']);

    # modulo de clientes
    Route::get('clientes', [App\Http\Controllers\ClientesController::class, 'index']);

    # modulo de tests
    Route::get('tests', [App\Http\Controllers\TestController::class, 'index']);

});

