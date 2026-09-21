# Laravel DB Translations

[![Latest Version](https://img.shields.io/packagist/v/togrul614/laravel-db-translations.svg)](https://packagist.org/packages/togrul614/laravel-db-translations)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

A database-backed translation manager for Laravel admin panels. It transparently overlays your file-based `trans()` / `__()` / `@lang()` translations with values stored in the database, and ships a minimal, framework-agnostic CRUD screen for managing them.

The package is intentionally unopinionated about styling: it doesn't depend on Bootstrap, Tailwind, Metronic, jQuery, or DataTables. The published views can be fully copied and restyled to match any admin panel, or simply plugged into your existing layout via a config option.

## Features

- Drop-in override of Laravel's translation loader — no changes needed to existing `trans()` / `__()` / `@lang()` calls
- Database values take priority over file translations; missing keys fall back to your `lang/*` files
- Simple CRUD admin screen (index, create, edit, delete) with publishable, unstyled Blade views
- Per-action authorization via Laravel's `Gate`, compatible with `spatie/laravel-permission` or any Gate-based permission system
- Configurable list of languages — either a static array or your own `Language` Eloquent model
- `translations:import` Artisan command to seed the database from existing `lang/*.php` and `lang/*.json` files
- Per group/locale caching with automatic cache invalidation on save/delete

## Requirements

- PHP 8.1+
- Laravel 10, 11, or 12
- A list of languages, either static or backed by your own model (see [Languages](#languages))

## Installation

Install via Composer:

```bash
composer require togrul614/laravel-db-translations
```

Laravel's package auto-discovery registers `TranslationsServiceProvider` automatically.

Then publish and run the config and migration:

```bash
php artisan vendor:publish --tag=db-translations-config
php artisan vendor:publish --tag=db-translations-migrations
php artisan migrate
```

`--tag=db-translations-views` and `--tag=db-translations-lang` are optional — only publish these if you want to customize the views or the package's own messages.

## Configuration (`config/db-translations.php`)

| Key | Purpose |
|---|---|
| `table` | Table name (default `translations`) |
| `register_routes`, `route_prefix`, `route_name_prefix`, `middleware` | Registration of the admin CRUD routes. Set `register_routes` to `false` to disable them and copy `routes/web.php` into your own app instead. |
| `abilities` | The permission name checked via `Gate::denies()` for each action. Works with `spatie/laravel-permission` and any Gate-based permission system. Set an action to `null` to skip its check entirely. |
| `languages` | The list of languages a translation can be entered in — either a static array (`['code' => 'az', 'name' => 'Azərbaycan']`) or the class name of your own `Language` model (see below). |
| `layout` | The admin layout your published views `@extends` (e.g. `layouts.backend.master`). Leave `null` to use the package's own minimal, unstyled layout. |
| `override_translator` | When `true`, `trans()` / `__()` / `@lang()` transparently overlay database translations on top of file translations. |
| `cache_prefix`, `cache_ttl` | Caching per group and locale. |
| `import_path` | The directory the `translations:import` command scans (default: `resource_path('lang')`). |

### Languages

Most projects already have their own `Language` model for managing available languages — the package doesn't duplicate that, it just uses it:

```php
// config/db-translations.php
'languages' => \App\Models\Language::class,
```

Your model should have `code` and `name` columns, and optionally a `scopeActive()` local scope to filter which languages are selectable when creating a translation.

Alternatively, provide a static array:

```php
'languages' => [
    ['code' => 'az', 'name' => 'Azərbaycan'],
    ['code' => 'en', 'name' => 'English'],
],
```

### Authorization (`abilities`)

Each admin action is checked with `Gate::denies($ability)`:

```php
'abilities' => [
    'index' => 'translations index',
    'create' => 'translations create',
    'edit' => 'translations edit',
    'delete' => 'translations delete',
],
```

This works out of the box with `spatie/laravel-permission` (via its `Gate::before` callback) or any other package that hooks into Laravel's Gate. Set an action to `null` to disable its check entirely.

### Matching your admin panel's design

There are two ways to fit the CRUD screens into your existing admin panel:

1. **Quick**: point `layout` at your own admin master Blade file (e.g. `'layout' => 'layouts.backend.master'`). The package's index/form views render inside a `@section('content')` block, which most admin layouts already support.
2. **Full control**: run `php artisan vendor:publish --tag=db-translations-views` to copy the views into `resources/views/vendor/db-translations/` and edit them freely (Bootstrap, Tailwind, Metronic, or anything else) — Laravel automatically prefers these published copies over the package's own views.

## Usage

Call translations exactly as you always have — nothing changes in your application code:

```php
trans('frontend.welcome_title');
__('welcome_title'); // group '*' — for JSON / single-word translations
```

### Importing existing translation files

To seed the database from your existing `resources/lang/*` files:

```bash
php artisan translations:import
```

This command reads both `lang/{locale}/{group}.php` files (as `group`) and `lang/{locale}.json` files (as the `*` group), and writes them into the database without overwriting existing database values — a file value is only used to fill in a locale that's still empty in the database.

### Linking to the admin screen

Use the configured route names to add a menu link:

```blade
<a href="{{ route(config('db-translations.route_name_prefix') . 'index') }}">Translations</a>
```

## Structure

```
src/
  Console/Commands/ImportTranslationsCommand.php
  Http/Controllers/TranslationController.php
  Models/Translation.php
  Services/DatabaseTranslationLoader.php
  Support/LanguageResolver.php
  TranslationsServiceProvider.php
config/db-translations.php
database/migrations/2024_01_01_000001_create_translations_table.php
resources/views/{layout,index,form}.blade.php
resources/lang/{az,en}/messages.php
routes/web.php
```

## License

This package is open-source software licensed under the [MIT license](LICENSE).
