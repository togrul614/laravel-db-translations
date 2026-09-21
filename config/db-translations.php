<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Table name
    |--------------------------------------------------------------------------
    */
    'table' => 'translations',

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    |
    | Set "register_routes" to false if you'd rather copy routes/web.php
    | into your own app and wire them up yourself.
    |
    */
    'register_routes' => true,
    'route_prefix' => 'backend/translations',
    'route_name_prefix' => 'backend.translations.',
    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    |
    | Each action is checked with Gate::denies($ability). Works out of the
    | box with spatie/laravel-permission (or any package that hooks into
    | Laravel's Gate), since it doesn't hard-code a permission driver.
    | Set an ability to null to skip the check entirely for that action.
    |
    */
    'abilities' => [
        'index' => 'translations index',
        'create' => 'translations create',
        'edit' => 'translations edit',
        'delete' => 'translations delete',
    ],

    /*
    |--------------------------------------------------------------------------
    | Languages
    |--------------------------------------------------------------------------
    |
    | The list of languages translations can be entered in. Provide either:
    |
    |   1) A static array of ['code' => 'az', 'name' => 'Azərbaycan'] rows, or
    |   2) The fully-qualified class name of your own Eloquent model that has
    |      "code" and "name" columns (e.g. App\Models\Language::class). If the
    |      model has an "active" scope (scopeActive), it will be used to
    |      filter the results.
    |
    */
    'languages' => [
        ['code' => 'az', 'name' => 'Azərbaycan'],
        ['code' => 'en', 'name' => 'English'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Blade layout the published views extend via @extends(...). Leave null
    | to use the package's minimal, unstyled fallback layout. Point it at
    | your own admin layout (e.g. "layouts.backend.master") to match your
    | panel's chrome without publishing the views at all.
    |
    */
    'layout' => null,

    /*
    |--------------------------------------------------------------------------
    | Translator loader override
    |--------------------------------------------------------------------------
    |
    | When true, the package wraps Laravel's translator so trans()/__()/@lang
    | transparently fall back to file-based translations, then overlay
    | whatever is stored in the database. Set to false if you want to call
    | Translation::getGroupTranslations() yourself instead.
    |
    */
    'override_translator' => true,

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    */
    'cache_prefix' => 'db_translations_',
    'cache_ttl' => 86400,

    /*
    |--------------------------------------------------------------------------
    | Import source
    |--------------------------------------------------------------------------
    |
    | Directory the "translations:import" command scans for existing
    | lang/*.php and lang/*.json files to seed the database from.
    |
    */
    'import_path' => null, // null => resource_path('lang')

];
