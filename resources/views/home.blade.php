<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link 
        rel="preload" 
        crossorigin
        href="{{ Vite::asset('resources/fonts/OpenSans-VariableFont_wdth,wght.ttf') }}" 
        as="font" />
    <link 
        rel="preload" 
        crossorigin
        href="{{ Vite::asset('resources/fonts/OpenSans-Italic-VariableFont_wdth,wght.ttf') }}" 
        as="font" />


    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    
</head>
    <body id="app">
    </body>
</html>