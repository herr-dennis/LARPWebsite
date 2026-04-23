@extends("layouts.default_layout")



@section("main_content")


    <div id="app" ></div>

    <p>{{$checked}}</p>
    @if($checked===true)

    <div class="defaultContainer"    >
        <p>Liste der Geschichten</p>
        <div class="defaultContainer__a" >
            <div >
                <a href="#1"  >Uniform Bauanleitung I</a>
                <a href="#2" >Uniform Bauanleitung II</a>
                <a href="#3" >Uniform Bauanleitung III</a>
                <a  href="#4" >Uniform Bauanleitung IV</a>
                <a  href="#5" >Wappen Tabab-Eckland</a>
            </div>

        </div>
    </div>
    <div class="trennlinie"></div>
    <div class="defaultContainer">
        <h1 id="1" >Uniform Bauanleitung I</h1>
        <p>Weißes Hemd als Grundlage</p>
        <img src="{{asset("images/bau_1.jpg")}}" alt="images/favicon.png"   >
    </div>

    <div class="defaultContainer">
        <h1 id="2" >Uniform Bauanleitung II</h1>
        <p>Bordeauxrotes Hemd als Grundlage:</p>
        <img src="{{asset("images/bau_2.jpg")}}" alt="images/favicon.png"   >
    </div>

    <div class="defaultContainer">
        <h1 id="3" >Uniform Bauanleitung III</h1>
        <p>Uniformshose: </p>
        <p>Engeschlitzt mit Strumpfhose: </p>
        <p>Oder weißer Stoffeinlage mit Kniestrümpfen: </p>
        <img src="{{asset("images/bau_3.jpg")}}" alt="images/favicon.png"   >
    </div>

    <div class="defaultContainer">
        <h1 id="4" >Uniform Bauanleitung IV</h1>
        <p>Um die schlichte Uniform abzurunden, sollte auf der Brust mindestens eine Weste getragen werden oder Hosenträger.  </p>
        <p>Keine nackten Hemden! </p>
        <img src="{{asset("images/bau_iv.jpg")}}" alt="images/favicon.png"   >
    </div>

    <div class="defaultContainer">
        <h1 id="5" >Wappen Tabab-Eckland</h1>
        <p>Wappenkunde in Eckland</p>
        <img src="{{asset("images/wappen.jpg")}}" alt="images/favicon.png"   >
    </div>



    @else

        <div  class="defaultContainer">
            <p>Sie müssen eingeloggt sein, um auf das OT-Zelt zugreifen zu können</p>
        </div>

    @endif

@endsection
