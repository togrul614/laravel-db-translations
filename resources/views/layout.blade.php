<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>@yield('title', trans('db-translations::messages.title'))</title>
    <style>
        /*
         * Minimal fallback styling only, used when db-translations.layout
         * is null. Publish the views (php artisan vendor:publish --tag=db-translations-views)
         * and delete this file once you point config('db-translations.layout')
         * at your own admin layout.
         */
        body { font-family: system-ui, sans-serif; margin: 2rem; color: #1a1a1a; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: .5rem; text-align: left; vertical-align: top; }
        .db-translations-actions { display: flex; gap: .5rem; }
        .db-translations-toolbar { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap; }
        .db-translations-alert { padding: .75rem 1rem; margin-bottom: 1rem; border: 1px solid #ccc; }
        .db-translations-field { margin-bottom: 1rem; }
        .db-translations-field label { display: block; font-weight: bold; margin-bottom: .25rem; }
        .db-translations-field textarea, .db-translations-field input { width: 100%; box-sizing: border-box; padding: .4rem; }
        .db-translations-error { color: #b00020; font-size: .875rem; }
    </style>
</head>
<body>
@yield('content')
</body>
</html>
