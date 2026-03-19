@extends("layouts.default_layout")

@section("main_content")
    <h1>POST!  {{$topicId}} und {{$rubrikId}}  </h1>
    <div id="app" data-page="forum_posts"  data-topic-id="{{$topicId}}"   data-rubrik-id="{{$rubrikId}}"     > </div>
@endsection
