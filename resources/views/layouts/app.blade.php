<!doctype html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
  </head>
  <body class="font-sans antialiased text-black leading-tight">
    @include('components.nav')
    <div id="app" class="min-h-screen">
      @yield('body')
      <flash message="{{ session('flash') }}"></flash>
    </div>
    @include('components.footer')
    <script src="{{ mix('js/app.js') }}"></script>
  </body>
</html>
