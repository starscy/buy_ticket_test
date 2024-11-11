<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/orders', [OrderController::class, 'store'])->middleware(['auth', 'verified']);


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//api бронирования
Route::post('/api/book', function (){

    $rand = rand(0,1);
    $resolve = ['message' => 'order successfully booked'];
    $error = ['error' => 'barcode already exists'];
    return $rand ? response()->json($resolve, 200) : response()->json($error, 406);
});

Route::post('/api/approve', function (Request $request){
    $barcode = $request->input('barcode');
    $rand = rand(0,1);
    $resolve = ['message' => 'order successfully aproved'];
    $error = ['error' => 'event cancelled'];
    return $rand ? response()->json($resolve, 200) : response()->json($error, 406);
});


require __DIR__.'/auth.php';
