<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#6366F1">
    <meta name="robots" content="index, follow">
    <meta name="author" content="EzyTools">

    {{-- Inertia manages title, description, keywords, OG, Twitter, and canonical via <Head> --}}
    <title inertia>{{ $page['props']['meta']['title'] ?? config('app.name') }}</title>

    {{-- Favicon --}}
    <link rel="icon"             type="image/png" href="{{ \App\Models\SiteSetting::getValue('site_favicon', '/favicon.png') }}">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="manifest"         href="/manifest.json">

    {{-- Fonts (preload for performance) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"></noscript>

    {{-- Admin Custom Head Scripts (Analytics, etc.) --}}
    {!! \App\Models\SiteSetting::getValue('header_scripts') !!}

    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>
<body class="font-sans antialiased">
    @inertia

    {{-- Admin Custom Footer Scripts --}}
    {!! \App\Models\SiteSetting::getValue('footer_scripts') !!}
</body>
</html>
