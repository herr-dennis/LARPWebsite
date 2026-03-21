<?php

use App\Models\DonnerfelsStory;
use App\Models\EcklandStory;
use App\Models\Pionier;
use App\Models\Rubric;
use App\Models\TalagradStory;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;


Route::get('/', function () {
    return view('main_page');
});
Route::get('/login', function () {
    return view('loginView');
});

Route::get('/ueber-uns/story', function () {
    return view('storyView');
});
Route::get('/medien', function () {
    return view('medienView');
});
Route::get('/ueber-uns/kontakt', function () {
    return view('kontaktView');
});
Route::get('/handwerk', function () {
    return view('handwerkView');
});



Route::POST('/login', function (Request $request) {

    //Kleiner Check
    //Einfach abbrechen, keine Informationen liefern.
    try{
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|min:2'
        ]);
    }catch (\Exception $e){
        return response()->json(["message" => "Fehler"],501);
    }

    //Lädt aus Users
    if(!Auth::attempt(['name' => request('name'), 'password' => request('password')])){
        return response()->json(["message" => "Login fehlgeschlagen"], 400);
    }

    //Neue Session ID verhindert die Übernahme
    $request->session()->regenerate();

    return response()->json(["message" => "Login erfolgreich"],201);


});

Route::get('/me', function () {
    if (!Auth::check()) {
        return response()->json([
            'message' => 'Nicht eingeloggt'
        ],401);
    }

    return response()->json(
        Auth::user()->only(['id', 'name', 'role'])
    );
});


Route::get("/ueber-uns/story/donnerfels",function (){
    return view("donnerfelsStoryView");
});
Route::get("/ueber-uns/story/eckland",function (){
    return view("ecklandStory");
});
Route::get("/ueber-uns/story/talagrad",function (){
    return view("talagradStoryView");
});

Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json([
        'message' => 'Logout erfolgreich'
    ]);
});

// FORUM

Route::get("/forum", function () {
    return view("forum/forumView");
})->name("forum");

Route::get("/ueber-uns/kontakt", function () {
    return view("kontaktView");
});
Route::get("/ueber-uns/pioniere", function () {
    return view("pioniereView");
});
Route::get("/medien/gallery", function () {
    return view("galleryView");
});
Route::get("/medien/videos", function () {
    return view("videosView");
});

Route::get("/medien/liedgut", function () {
    return view("liedgutView");
});

Route::get("/handwerk/metall", function () {
    return view("handwerkViews/metallView");
});
Route::get("/handwerk/holz", function () {
    return view("handwerkViews/holzView");
});
Route::get("/handwerk/ton", function () {
    return view("handwerkViews/tonView");
});
Route::get("/handwerk/waffenbau", function () {
    return view("handwerkViews/waffenbauView");
});



Route::get("/forum/rubrik/{rubric}/topics", function ($rubric) {
    return view("forum/topicView", ['topicId' => $rubric]);
});


Route::get("/forum/rubrik/{rubric}/topics/{topic}/posts", function ($rubric, $topic) {
    return view("forum/postsView", [
        'rubrikId' => $rubric,
        'topicId' => $topic
    ]);
});



/*
 *
 *      API  ////////////////////
 *
 */
//Holt alle Rubriken
Route::get("/api/forum/rubrik", function () {

    if (!Auth::check()) {
        return response()->json(["message" => "Unauthorized"], 401);
    }

    return Rubric::all();

});
//Zu einer Rubric die ganzen Topics
Route::get("/api/rubrik/{rubic}/topics",function($rubric){

    if (!Auth::check()) {
        return response()->json(["message" => "Unauthorized"], 401);
    }

    $topics = \App\Models\Rubric::query()
        ->join('topics', 'topics.rubric_id', '=', 'rubrics.id')->where("rubrics.id","=",$rubric)
        ->get();

    return $topics;

});

//Holt alle Posts aus Topic
Route::get("/api/rubrik/{rubic}/topics/{topic}/posts",function($rubric , $topics){

    if (!Auth::check()) {
        return response()->json(["message" => "Unauthorized"], 401);
    }

        return $posts = Topic::query()->find($topics)->posts;

})->whereNumber("rubic");



Route::post("/api/forum/rubrik",function(Request $request){
    if (!Auth::check()) {
        return response()->json(["message" => "Unauthorized"], 401);
    }

    $verfasser = request("verfasser");
    $status = true;
    $rubric_name = request("rubrik");


    try{

        Rubric::query()->create([
            "rubric_name" => $rubric_name,
            "rubric_verfasser" => $verfasser,
            "rubric_status" => $status
        ]);

    }catch (\Exception $e){
        return response()->json(["message" => $e->getMessage()], 500);
    }

    return response()->json(Rubric::all(), 200);
});

Route::delete("/api/forum/rubrik/{rubric}",function($rubric){
    if (!Auth::check()) {
        return response()->json(["message" => "Unauthorized"], 401);
    };
    try {
        Rubric::query()->where("id","=",$rubric)->delete();
    }catch (\Exception $e){
        return response()->json(["message" => $e->getMessage()], 500);
    }
    return response()->json(Rubric::all(), 200);
});



Route::post('/api/ueber-uns/donnerfels', function (Request $request) {
    if (!Auth::check()) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $path = null;

    try {
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            if ($image->isValid()) {
                $path = $image->store('DonnerfelsStory', 'public');
            }
        }
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 502);
    }

    try {
        $story = DonnerfelsStory::query()->create([
            'text' => $request->input('text'),
            'image' => $path,
            'title' => $request->input('title'),
            'position' => (DonnerfelsStory::max('position') ?? 0) + 1,
        ]);
    } catch (\Exception $e) {
        return response()->json(['message' => "Fehler in der Datenbank"], 500);
    }

    return response()->json([
        'message' => 'Story gespeichert',
        'story' => $story,
    ], 200);
});



Route::get("/api/ueber-uns/donnerfels",function(Request $request){

   try{
       $data = DonnerfelsStory::all();
       return response()->json($data, 200);

   }catch (\Exception $e){
       return response()->json(["message" => "Fehler in der Datenbank"], 500);
   }

});

Route::get("/api/ueber-uns/talagrad",function(Request $request){

    try{
        $data = TalagradStory::all();
        return response()->json($data, 200);

    }catch (\Exception $e){
        return response()->json(["message" => "Fehler in der Datenbank"], 500);
    }

});

Route::post('/api/ueber-uns/talagrad', function (Request $request) {
    if (!Auth::check()) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $path = null;

    try {
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            if ($image->isValid()) {
                $path = $image->store('TalagradStory', 'public');
            }
        }
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 502);
    }

    try {
        $story = TalagradStory::query()->create([
            'text' => $request->input('text'),
            'image' => $path,
            'title' => $request->input('title'),
            'position' => (TalagradStory::max('position') ?? 0) + 1,
        ]);
    } catch (\Exception $e) {
        return response()->json(['message' => "Fehler in der Datenbank"], 500);
    }

    return response()->json([
        'message' => 'Story gespeichert',
        'story' => $story,
    ], 200);
});

Route::get("/api/ueber-uns/eckland",function(Request $request){

    try{
        $data = EcklandStory::all();
        return response()->json($data, 200);

    }catch (\Exception $e){
        return response()->json(["message" => "Fehler in der Datenbank"], 500);
    }

});

Route::post('/api/ueber-uns/eckland', function (Request $request) {
    if (!Auth::check()) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $path = null;

    try {
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            if ($image->isValid()) {
                $path = $image->store('EcklandStory', 'public');
            }
        }
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 502);
    }

    try {
        $story = EcklandStory::query()->create([
            'text' => $request->input('text'),
            'image' => $path,
            'title' => $request->input('title'),
            'position' => (EcklandStory::max('position') ?? 0) + 1,
        ]);
    } catch (\Exception $e) {
        return response()->json(['message' => "Fehler in der Datenbank"], 500);
    }

    return response()->json([
        'message' => 'Story gespeichert',
        'story' => $story,
    ], 200);
});




Route::delete("/api/ueber-uns/{name}/{id}",function(Request $request ,$name,$id){

    if (!Auth::check()) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }


    try{


    if($name == "eckland"){

        EcklandStory::query()->where('id',$id)->delete();
        return response()->json(["message" =>"Erfolgreich"],200);

    }
    elseif ($name == "talagrad"){
        TalagradStory::query()->where('id',$id)->delete();
        return response()->json(["message" =>"Erfolgreich"],200);

    }elseif ($name == "donnerfels"){
        DonnerfelsStory::query()->where('id',$id)->delete();
        return response()->json(["message" =>"Erfolgreich"],200);
    }
    }catch (\Exception $e){
        return response()->json(['message' => $e->getMessage()], 500);
    }

    return response()->json(["message" => "API-Call nicht gefunden"], 400);
})->where('name' , 'donnerfels' | 'eckland' | 'talagrad');




Route::get("/api/ueber-uns/pioniere",function(){

    try{
        $data = Pionier::all();
        return response()->json($data, 200);

    }catch (\Exception $e){
        return response()->json(["message" => "Fehler in der Datenbank"], 500);
    }

});



Route::post("/api/ueber-uns/pioniere",function(Request $request){

    if (!Auth::check() || (Auth::user()->role === 2 ) || (Auth::user()->role ===3)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }



    $name = $request->input('name') ;
    $text = $request->input('text') ;
    $waffen = $request->input('waffen') ;
    $geburtstag = $request->input('geburtstag') ;
    $rang  = $request->input('rang');
    $dienstjahre = $request->input('dienstjahre');


    $path = null;

    try {
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            if ($image->isValid()) {
                $path = $image->store('Pioniere', 'public');
            }
        }
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 502);
    }

    try{
        $data = Pionier::query()->create([
            'name' => $name,
            'text' => $text,
            'waffen' => $waffen,
            'geburtstag' => $geburtstag,
            'dienstjahre' => $dienstjahre,
            'rang' => $rang,
            'image' => $path

        ]);
        $newData= Pionier::all();
        return response()->json($newData, 200);

    }catch (\Exception $e){
        return response()->json(["message" => $e->getMessage()], 500);
    }

});



Route::delete("/api/ueber-uns/pioniere/{id}",function(Request $request,  $id){

    if (!Auth::check()&& Auth::user()->role ===1) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    try{

        Pionier::query()->where('id',$id)->delete();
        $data = Pionier::all();
        return response()->json($data);

    }catch (Exception $e){
        return response()->json(['message' => "Datenfehler"], 500);
    }

});
