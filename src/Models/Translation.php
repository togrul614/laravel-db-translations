<?php

namespace Globalsoft\DbTranslations\Models;

use Globalsoft\DbTranslations\Support\LanguageResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class Translation extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function getTable()
    {
        return config('db-translations.table', 'translations');
    }

    protected static function booted()
    {
        static::saved(fn () => static::clearTranslationCache());
        static::deleted(fn () => static::clearTranslationCache());
    }

    public static function clearTranslationCache(): void
    {
        $groups = static::query()->select('group')->distinct()->pluck('group')->toArray();
        $groups[] = '*';

        $locales = array_unique(array_merge(
            LanguageResolver::codes(),
            array_filter([config('app.locale'), config('app.fallback_locale')])
        ));

        $prefix = config('db-translations.cache_prefix', 'db_translations_');

        foreach ($groups as $group) {
            foreach ($locales as $locale) {
                Cache::forget("{$prefix}{$locale}_{$group}");
            }
        }
    }

    public static function getGroupTranslations(string $group, string $locale): array
    {
        $results = [];

        foreach (static::query()->where('group', $group)->get() as $translation) {
            $value = $translation->value[$locale] ?? null;

            if ($value === null) {
                continue;
            }

            if ($group === '*') {
                $results[$translation->key] = $value;
            } else {
                Arr::set($results, $translation->key, $value);
            }
        }

        return $results;
    }

    public function getTranslation(string $locale, ?string $default = ''): ?string
    {
        return $this->value[$locale] ?? $default;
    }
}
