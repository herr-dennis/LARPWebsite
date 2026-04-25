@extends("layouts.default_layout")

@section("title", $data["name"])

@section("main_content")

    <div id="app" data-page="default"></div>
    <div class="defaultContainer" >

        @foreach(explode("\n", $data->text) as $line)
            <p>{{ $line }}</p>
        @endforeach

    </div>

@endsection
