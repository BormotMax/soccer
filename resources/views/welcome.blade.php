<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="{{ url('css/main.css') }}">
        <title>League table</title>
    </head>
    <body>
        <div id="app">
            <league-table />
        </div>
        <script src="{{asset('js/app.js')}}">
        </script>
    </body>
</html>
