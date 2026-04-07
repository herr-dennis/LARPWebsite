@extends("layouts.default_layout")



@section("header")

@endsection
@section("main_content")
    <div class="banner-container">
        <h1 class="banner-container__title">
            Willkommen auf unserer Website! <br>
            Wir sind eine deutsche LARP-Gruppe im düsteren Warhammer-Universum. <br>
        </h1>
    </div>
    <div class="vue-mount" data-component="Gallery"  ></div>
    <div id="app" data-page="home"></div>

<div class="vue-mount" data-component="Newsfeed"  ></div>

@endsection
