# Laravel DB Translations

Verilənlər bazasında saxlanılan, admin paneldən idarə olunan tərcümə (translation) meneceri. `trans()` / `__()` / `@lang()` çağırışlarını şəffaf şəkildə fayl-əsaslı tərcümələrlə verilənlər bazasındakı tərcümələrin üzərinə yazaraq birləşdirir, üstəlik ayrıca CRUD admin ekranı təqdim edir.

Paket qəsdən minimal saxlanılıb: heç bir CSS framework-ə (Bootstrap/Tailwind/Metronic) və ya JS kitabxanasına (jQuery, DataTables) bağlı deyil. Bütün view-lar `vendor:publish` ilə tam kopyalanıb istənilən admin panelin öz dizaynına uyğunlaşdırıla bilər, ya da sadəcə `layout` konfiqurasiyası ilə mövcud admin layout-unuza qoşula bilər.

## Tələblər

- PHP 8.1+
- Laravel 10, 11 və ya 12
- `languages` massivi/modeli tərəfindən idarə olunan bir dil siyahısı (bax aşağıda **Dillər**)

## Quraşdırma

Paket hələ Packagist-də deyil, GitLab-da (`git.globalhost.az`) saxlanılır. İstifadə edən layihənin `composer.json`-una VCS repository əlavə edin:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "http://git.globalhost.az/globalsoft01/laravel-db-translations.git"
        }
    ]
}
```

Sonra:

```bash
composer require globalsoft/laravel-db-translations
```

Laravel-in paket auto-discovery mexanizmi `TranslationsServiceProvider`-i özü qeydiyyatdan keçirəcək.

### Yerli inkişaf üçün (path repository)

Paketi push etməzdən əvvəl lokal test etmək istəyirsinizsə, `path` repository istifadə edin:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../laravel-db-translations"
        }
    ]
}
```

```bash
composer require globalsoft/laravel-db-translations:@dev
```

## Quraşdırmadan sonra

```bash
php artisan vendor:publish --tag=db-translations-config
php artisan vendor:publish --tag=db-translations-migrations
php artisan migrate
```

`--tag=db-translations-views` və `--tag=db-translations-lang` isteğe bağlıdır — yalnız view-ları və ya mesajları dəyişmək istəsəniz publish edin.

## Konfiqurasiya (`config/db-translations.php`)

| Açar | Nə üçündür |
|---|---|
| `table` | Cədvəl adı (default `translations`) |
| `register_routes`, `route_prefix`, `route_name_prefix`, `middleware` | Admin CRUD marşrutlarının qeydiyyatı. Öz marşrutlarınızı yazmaq istəsəniz `register_routes` = `false` edib `routes/web.php`-ı özünüz kopyalayın. |
| `abilities` | Hər əməliyyat üçün `Gate::denies()` ilə yoxlanılan icazə adı. `spatie/laravel-permission` daxil olmaqla, Laravel Gate-ə qoşulan istənilən icazə sistemi ilə işləyir. Bir əməliyyatı `null` etsəniz, o yoxlama tamamilə keçilir. |
| `languages` | Tərcümə daxil edilə bilən dillərin siyahısı — ya statik massiv (`['code' => 'az', 'name' => 'Azərbaycan']`), ya da öz `Language` modelinizin class adı (bax aşağıda). |
| `layout` | Published view-ların `@extends` edəcəyi öz admin layout-unuz (məs. `layouts.backend.master`). `null` olarsa paketin öz minimal, stilsiz layout-u istifadə olunur. |
| `override_translator` | `true` olduqda `trans()`/`__()`/`@lang()` avtomatik olaraq bazadakı tərcümələri fayl tərcümələrinin üzərinə yazır. |
| `cache_prefix`, `cache_ttl` | Qrup+dil üzrə keşləmə. |
| `import_path` | `translations:import` əmrinin skan edəcəyi qovluq (default: `resource_path('lang')`). |

### Dillər (`languages`)

Bu layihələrdə çox vaxt artıq öz `Language` modeliniz (dillərin idarəçiliyi üçün) mövcud olur — paket bunu təkrarlamır, sadəcə istifadə edir:

```php
// config/db-translations.php
'languages' => \App\Models\Language::class,
```

Modelinizdə `code` və `name` sütunları, istəyə görə `scopeActive()` (aktiv dilləri filtrləmək üçün) olmalıdır — məhz `addglobal` layihəsindəki `App\Models\Language` modeli bu şərtlərə artıq uyğundur.

### İcazələr (`abilities`)

Default dəyərlər `addglobal` layihəsindəki konvensiyaya uyğundur (`translations index/create/edit/delete`, `Gate::denies()` vasitəsilə yoxlanılır — bu, `spatie/laravel-permission`-un avtomatik qoşduğu `Gate::before` callback-i ilə işləyir). Başqa admin panel fərqli permission adları istifadə edirsə, sadəcə `abilities` massivini dəyişin.

### Dizaynı öz admin panelinizə uyğunlaşdırmaq

İki yol var:

1. **Sürətli**: `layout` konfiqurasiyasını öz admin master blade faylınıza yönləndirin (məs. `'layout' => 'layouts.backend.master'`). Paketin index/form view-ları `@section('content')` bloku ilə işləyir, əksər admin layout-ları bunu dəstəkləyir.
2. **Tam nəzarət**: `php artisan vendor:publish --tag=db-translations-views` ilə view-ları `resources/views/vendor/db-translations/` altına kopyalayın və istədiyiniz kimi (Bootstrap, Tailwind, Metronic və s.) redaktə edin — Laravel avtomatik olaraq bu kopyaları paketin öz view-larından üstün tutacaq.

## İstifadə

Kodda tərcümələri həmişəki kimi çağırın, heç nə dəyişmir:

```php
trans('frontend.welcome_title');
__('welcome_title'); // group = '*' (JSON / tək sözlər)
```

Mövcud `resources/lang/*` fayllarınızı bazaya köçürmək üçün:

```bash
php artisan translations:import
```

Bu əmr həm `lang/{locale}/{group}.php` fayllarını (`group` kimi), həm də `lang/{locale}.json` fayllarını (`*` qrupu kimi) oxuyub bazaya yazır, mövcud baza qeydlərini üstələmədən (fayl dəyəri yalnız baza dəyəri boş olduqda istifadə olunur).

Admin panelə menyu linki əlavə etmək üçün marşrut adlarından istifadə edin:

```blade
<a href="{{ route(config('db-translations.route_name_prefix') . 'index') }}">Tərcümələr</a>
```

## Struktur

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
