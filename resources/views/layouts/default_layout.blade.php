<!doctype html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/css_main/main.scss', 'resources/js/js_main/main.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Taalagard</title>
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


<footer>
    <div class="footer">
        <a class="footer__a">Impressum</a>
        <a class="footer__a">Datenschutz</a>
        <a class="footer__a">Schwarz&Web</a>
    </div>
</footer>

</body>




</html>

