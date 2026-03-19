<?php

use App\Models\Rubric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/pictures', function () {

    $path = storage_path('app/public/gallery');

    if (!File::exists($path)) {
        return response()->json([]);
    }

    $files = File::files($path);

    $urls = collect($files)->map(function ($file) {
        return asset('storage/gallery/' . $file->getFilename());
    });

    return response()->json($urls);
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













