<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/css_main/main.scss', 'resources/js/js_main/main.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Startseite')</title>
    <link rel="icon" type="image/x-icon" href={{asset("images/favicon.png")}}>
    <meta name="robots" content="index, follow">


</head>


<header id="nav-target">

     @section("header")
    @show

</header>

<body>

<main>
    @section("main_content")
    @show
</main>


<footer  class="footer-container" >

    <div class="footer">
        <a href="/Impressum"  class="footer__a">Impressum</a>
        <a  href="/Datenschutz" class="footer__a">Datenschutz</a>
        <a class="footer__a">Schwarz&Web</a>
    </div>

</footer>

</body>




</html>

