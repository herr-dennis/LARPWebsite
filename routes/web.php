<?php

use App\Mail\ContactFormMail;
use App\Models\DonnerfelsStory;
use App\Models\EcklandStory;
use App\Models\Pionier;
use App\Models\Posts;
use App\Models\Rubric;
use App\Models\TalagradStory;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| NORMALE WEB-ROUTEN
|--------------------------------------------------------------------------
| Öffentliche Seiten / Views
|
*/

Route::get('/', function () {
    return view('main_page');
});

Route::get('/login', function () {
    return view('loginView');
});

Route::get("/Datenschutz", function () {
    return view('datenschutzView');
});

Route::get("/Impressum", function () {
    return view('impressumview');
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
Route::get('/ueber-uns/ot-zelt', function () {
    return view('otZeltView');
});
Route::get('/handwerk', function () {
    return view('handwerkView');
});

Route::get('/admin', function () {
    return view('adminView');
});

/*
|--------------------------------------------------------------------------
| STORY-SEITEN
|--------------------------------------------------------------------------
| Einzelne Story-Unterseiten
|
*/

Route::get("/ueber-uns/story/donnerfels",function (){
    return view("donnerfelsStoryView");
});

Route::get("/ueber-uns/story/eckland",function (){
    return view("ecklandStory");
});

Route::get("/ueber-uns/story/talagrad",function (){
    return view("talagradStoryView");
});

/*
|--------------------------------------------------------------------------
| WEITERE ÖFFENTLICHE SEITEN
|--------------------------------------------------------------------------
| Kontakt, Pioniere, Medien-Unterseiten, Handwerk-Unterseiten
|
*/

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

/*
|--------------------------------------------------------------------------
| AUTH-ROUTEN
|--------------------------------------------------------------------------
| Login / Logout / aktueller Benutzer
|
*/

Route::POST('/login', function (Request $request) {

    //Kleiner Check
    //Einfach abbrechen, keine Informationen liefern.
    try{
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:2'
        ]);
    }catch (\Exception $e){
        return response()->json(["message" => "Fehler"],501);
    }

    //Lädt aus Users
    if(!Auth::attempt(['email' => request('email'), 'password' => request('password')])){
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

Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json([
        'message' => 'Logout erfolgreich'
    ]);
});

/*
|--------------------------------------------------------------------------
| FORUM-SEITEN
|--------------------------------------------------------------------------
| Views für Forum, Topics und Posts
|
*/

Route::get("/forum", function () {
    return view("forum/forumView");
})->name("forum");

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
|--------------------------------------------------------------------------
| API-ROUTEN
|--------------------------------------------------------------------------
| Ab hier beginnt der API-Bereich
|
*/

/*
|--------------------------------------------------------------------------
| API - FORUM
|--------------------------------------------------------------------------
| Rubriken, Topics, Posts
|
*/

//Holt alle Rubriken
Route::get("/api/forum/rubrik", function () {

    if (!Auth::check()) {
        return response()->json(["message" => "Unauthorized"], 401);
    }

    return Rubric::all()->sortByDesc("created_at")->values();

});

//Zu einer Rubric die ganzen Topics
Route::get("/api/rubrik/{rubic}/topics",function($rubric){

    if (!Auth::check()) {
        return response()->json(["message" => "Unauthorized"], 401);
    }

    $topics = \App\Models\Rubric::query()
        ->join('topics', 'topics.rubric_id', '=', 'rubrics.id')->where("rubrics.id","=",$rubric)
        ->get()->sortByDesc("created_at")->values();

    return $topics;

});

//Holt alle Posts aus Topic
Route::get("/api/rubrik/{rubic}/topics/{topic}/posts",function($rubric , $topics){

    if (!Auth::check()) {
        return response()->json(["message" => "Unauthorized"], 401);
    }

    return $posts = Topic::query()->find($topics)->posts;

})->whereNumber("rubic");

Route::post("/api/rubrik/{rubic}/topics/{topic}/posts",function( Request $request , $rubric , $topics){

    if (!Auth::check() || Auth::user()->role === 3) {
        return response()->json(["message" => "Unauthorized"], 401);
    }

    $text = $request->input("text");
    $verfasser = $request->input("verfasser");

    if($text==="" || $text === null || $verfasser==="" || $verfasser === null ){
        return response()->json(["message" => "Fehler kein Text"], 402);
    }


    try {
        Posts::query()->create([
            'topic_id' => $topics,
            'post_text'=> $text,
             'post_author' => $verfasser
        ]);

    }catch (\Exception $e){
        return response()->json(["message" => $e->getMessage()], 500);
    }

    $posts = Topic::query()->find($topics)->posts;

    return response()->json($posts);

});






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

    return response()->json(Rubric::all()->sortByDesc("created_at")->values(), 200);
});
Route::post("/api/forum/rubrik/{id}/topic",function(Request $request , $id){
    if (!Auth::check()) {
        return response()->json(["message" => "Unauthorized"], 401);
    }

    $verfasser = request("verfasser");
    $status = true;
    $rubric_name = request("topic");


    try{

       Topic::query()->create([
            "topic_name" => $rubric_name,
            "topic_verfasser" => $verfasser,
            "topic_status" => $status,
             "rubric_id" => $id
        ]);

    }catch (\Exception $e){
        return response()->json(["message" => $e->getMessage()], 500);
    }

    $topics = \App\Models\Rubric::query()
        ->join('topics', 'topics.rubric_id', '=', 'rubrics.id')->where("rubrics.id","=",$id)
        ->get()->sortByDesc("created_at")->values();

    return $topics;
});

Route::delete("/api/forum/rubrik/{rubric}",function($rubric){
    if (!Auth::check() || Auth::user()->role ===2 || Auth::user()->role==3) {
        return response()->json(["message" => "Unauthorized"], 401);
    };
    try {
        Rubric::query()->where("id","=",$rubric)->delete();
    }catch (\Exception $e){
        return response()->json(["message" => $e->getMessage()], 500);
    }
    return response()->json(Rubric::all(), 200);
});

Route::delete("/api/forum/rubric/{rubrik_id}/topics/{topic_id}",function($rubric_id , $topic_id){
    if (!Auth::check() || Auth::user()->role ===2 || Auth::user()->role==3) {
        return response()->json(["message" => "Unauthorized"], 401);
    };
    try {
        Topic::query()->where("id","=",$topic_id)->delete();
    }catch (\Exception $e){
        return response()->json(["message" => $e->getMessage()], 500);
    }

    $topics = \App\Models\Rubric::query()
        ->join('topics', 'topics.rubric_id', '=', 'rubrics.id')->where("rubrics.id","=",$rubric_id)
        ->get()->sortByDesc("created_at")->values();

    return $topics;
});




/*
|--------------------------------------------------------------------------
| API - STORYS / ÜBER UNS
|--------------------------------------------------------------------------
| Donnerfels, Talagrad, Eckland
|
*/

// -------------------- DONNERFELS --------------------

Route::post('/api/ueber-uns/donnerfels', function (Request $request) {
    if (!Auth::check() || Auth::user()->role ===2 || Auth::user()->role==3) {
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

// -------------------- TALAGRAD --------------------

Route::get("/api/ueber-uns/talagrad",function(Request $request){

    try{
        $data = TalagradStory::all();
        return response()->json($data, 200);

    }catch (\Exception $e){
        return response()->json(["message" => "Fehler in der Datenbank"], 500);
    }

});

Route::post('/api/ueber-uns/talagrad', function (Request $request) {
    if (!Auth::check() || Auth::user()->role ===2 || Auth::user()->role==3) {
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

// -------------------- ECKLAND --------------------

Route::get("/api/ueber-uns/eckland",function(Request $request){

    try{
        $data = EcklandStory::all();
        return response()->json($data, 200);

    }catch (\Exception $e){
        return response()->json(["message" => "Fehler in der Datenbank"], 500);
    }

});

Route::post('/api/ueber-uns/eckland', function (Request $request) {
    if (!Auth::check() || Auth::user()->role ===2 || Auth::user()->role==3) {
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

// -------------------- STORY LÖSCHEN --------------------

Route::delete("/api/ueber-uns/{name}/{id}",function($name,$id){

    if (!Auth::check() || Auth::user()->role ===2 || Auth::user()->role==3) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    try{

        if($name == "eckland"){

            $story = EcklandStory::query()->where('id', $id)->first();
            if (!$story) {
                return response()->json(['message' => 'Eintrag nicht gefunden'], 404);
            }

            if ($story->image) {
                $tmp = Storage::disk('public')->delete($story->image);
            }
            $story->delete();
            return response()->json(EcklandStory::all());
        }
        elseif ($name == "talagrad"){

            $story = TalagradStory::query()->where('id', $id)->first();

            if (!$story) {
                return response()->json(['message' => 'Eintrag nicht gefunden'], 404);
            }
            if ($story->image) {
                $tmp = Storage::disk('public')->delete($story->image);
            }
            $story->delete();
            return response()->json(TalagradStory::all());

        }elseif ($name == "donnerfels"){
            $story = DonnerfelsStory::query()->where('id', $id)->first();

            if (!$story) {
                return response()->json(['message' => 'Eintrag nicht gefunden'], 404);
            }
            if ($story->image) {
                $tmp = Storage::disk('public')->delete($story->image);
            }
            $story->delete();
            return response()->json(DonnerfelsStory::all());
        }
    }catch (\Exception $e){
        return response()->json(['message' => $e->getMessage()], 500);
    }

    return response()->json(["message" => "API-Call nicht gefunden"], 400);
})->where('name', 'donnerfels|eckland|talagrad');

/*
|--------------------------------------------------------------------------
| API - PIONIERE
|--------------------------------------------------------------------------
| Pioniere laden, anlegen, löschen
|
*/

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

    if (!Auth::check() || Auth::user()->role ===2 || Auth::user()->role==3) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    try{

        $Pionier = Pionier::query()->where('id',$id)->first();

        if($Pionier->image){
            Storage::disk('public')->delete($Pionier->image);
        }
        $Pionier->delete();
        $data = Pionier::all();
        return response()->json($data);

    }catch (Exception $e){
        return response()->json(['message' => "Datenfehler"], 500);
    }

});
/*
|--------------------------------------------------------------------------
| API - Kontakt
|--------------------------------------------------------------------------
|
*/


Route::post("/api/ueber-uns/kontakt",function(Request $request){

    // Honeypot check
    if (!empty($request->input('website'))) {
        return response()->json(['message' => 'Spam erkannt'], 400);
    }
    $data = $request->only(['name', 'email', 'text']);
      $name = $request->input('name') ;
      $text = $request->input('text') ;
      $email = $request->input('email') ;


    try{
        Mail::to('info@schwarz-und-web.de')
            ->send(new ContactFormMail($name,$text,$email));
    }
    catch (\Exception $e){
        return response()->json(['message' => $e->getMessage()], 500);
    }




    return response()->json(['message' => 'Gesendet']);


});




/*
|--------------------------------------------------------------------------
| API - SONSTIGES / DEBUG
|--------------------------------------------------------------------------
| Hilfs- oder Debug-Routen
|
*/

Route::get("/api/users", function (){
    if (!Auth::check() || Auth::user()->role ===2 || Auth::user()->role==3) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    try {
        $users = \App\Models\User::query()->select("id","role","name")->get();
    }catch (\Exception $e){
        return response()->json(['message' => $e->getMessage()], 500);
    }

    return response()->json($users);

});


Route::post("/api/users/{id}/role", function (Request $request, $id) {

    if (!Auth::check() || Auth::user()->role !== 1) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $request->validate([
        'role' => 'required|integer|min:1|max:3'
    ]);

    try {
        $user = \App\Models\User::query()->findOrFail($id);

        if ($user->id === Auth::id()) {
            return response()->json(['message' => 'You cannot change your own role'], 403);
        }

        $user->role = $request->role;
        $user->save();

        $users =\App\Models\User::query()->select("id","role","name")->get();

    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }

    return response()->json($users);
});

Route::delete("/api/users/{id}", function ($id) {

    if (!Auth::check() || Auth::user()->role !== 1) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    try {
        $user = \App\Models\User::query()->findOrFail($id);

        if ($user->role === 1) {
            return response()->json(['message' => 'Admin cannot be deleted'], 403);
        }

        if ($user->id === Auth::id()) {
            return response()->json(['message' => 'You cannot delete yourself'], 403);
        }

        $user->delete();
        $users =\App\Models\User::query()->select("id","role","name")->get();
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }

    return response()->json($users);
});


Route::get("/api/all/{name}",function($name){

    $files  =  Storage::disk("public")->allFiles();

    $files = array_filter($files, function ($file) {
        return $file !== '.gitignore';
    });

    dd($files);

});

/*
|--------------------------------------------------------------------------
| API - Gallery
|--------------------------------------------------------------------------
|
*/

Route::get("/api/storage/pics",function(){
    $files = Storage::disk("public")->files("Gallery");
    $files = array_filter($files, function ($file) {
        return $file !== '.gitignore';
    });
    $files = array_values($files);

    $files = array_map(function ($file) {
        return Storage::url($file);
    }, $files);

    return response()->json($files);
});


Route::post("/api/storage",function(Request $request){
    if (!Auth::check() || Auth::user()->role !== 1) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

  if(!$request->hasFile('file')){
      return response()->json(['message' => 'File not found'], 404);
  }

  $file = $request->file('file');
    try {
      $path =  Storage::disk('public')->putFile("Gallery",$file);
    }catch (\Exception $e){
        return response()->json(['message' => $e->getMessage()], 500);
    }

    return response()->json(['message' => $path]);
});


Route::post("/api/storage/multi",function(Request $request)
{

    if (!Auth::check() || Auth::user()->role !== 1) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    try {
    $request->validate([
        'files' => ['required', 'array'],
        'files.*' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf,webp'],
    ]);
    } catch (Exception $e) {
    return response()->json([
        'message' => 'Validation failed',
        'errors' => $e->getMessage()
    ], 422);
}


    $uploadedFiles = [];

    foreach ($request->file('files') as $file) {
        $path = $file->store('Gallery', 'public');

        $uploadedFiles[] = [
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'url' => '/storage/' . $path
        ];
    }

    $files = Storage::disk("public")->files("Gallery");
    $files = array_filter($files, function ($file) {
        return $file !== '.gitignore';
    });
    $files = array_values($files);
    $files = array_map(function ($file) {
        return Storage::url($file);
    }, $files);

    return response()->json($files);
});
/*
 * Bekommt file Parameter, file ist URL+Filename
 */
Route::delete("/api/storage/multi",function(Request $request){
    if (!Auth::check() || Auth::user()->role !== 1) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    if(!$request->has('file')){
        return response()->json(['message' => 'File not found'], 404);
    }

    $file = str_replace('/storage/', '', $request->input('file'));

    try {
        Storage::disk('public')->delete($file);
    }
    catch (\Exception $e){
        return response()->json(['message' => $e->getMessage()], 500);
    }

    $files = Storage::disk("public")->files("Gallery");
    $files = array_filter($files, function ($file) {
        return $file !== '.gitignore';
    });
    $files = array_values($files);
    $files = array_map(function ($file) {
        return Storage::url($file);
    }, $files);

    return response()->json($files);

});
