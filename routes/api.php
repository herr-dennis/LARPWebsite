<?php

use App\Models\Rubric;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/pictures', function () {
    $files = Storage::disk('public')->files('Gallery');

    $files = array_filter($files, function ($file) {
        return basename($file) !== '.gitignore';
    });

    $files = array_values($files);

    if (count($files) > 10) {
        $files = Arr::random($files, 10);
    }

    return response()->json($files);
});


Route::POST('/register', function (Request $request) {

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6|confirmed'
    ]);

    try{
        $user = \App\Models\User::query()->create([
            'name' => request('name'),
            'email' => request('email'),
            'password' => bcrypt(request('password')),
        ]);


    }catch (\Exception $e){
        return response()->json([
            'message' => 'Datenbankfehler'
        ], 500);
    }


    return response()->json([
        'message' => 'Registrierung erfolgreich',
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]
    ]);
});




Route::get("/rubrik", function () {

    if (!Auth::check()) {
        return response()->json(["message" => "Unauthorized"], 401);
    }

    return Rubric::all();

});













