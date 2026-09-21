<?php

namespace Togrul614\DbTranslations\Services;

use Togrul614\DbTranslations\Models\Translation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Translation\FileLoader;

class DatabaseTranslationLoader extends FileLoader
{
    /**
     * Load file-based translations, then overlay whatever is stored in the
     * database for the root namespace ("*"), i.e. what @lang()/trans()/__()
     * resolve for a given locale + group.
     */
    public function load($locale, $group, $namespace = null)
    {
        $fileTranslations = parent::load($locale, $group, $namespace);

        if ($namespace !== null && $namespace !== '*') {
            return $fileTranslations;
        }

        try {
            $prefix = config('db-translations.cache_prefix', 'db_translations_');
            $ttl = config('db-translations.cache_ttl', 86400);

            $dbTranslations = Cache::remember(
                "{$prefix}{$locale}_{$group}",
                $ttl,
                fn () => Translation::getGroupTranslations($group, $locale)
            );

            if (! empty($dbTranslations)) {
                return array_replace_recursive($fileTranslations, $dbTranslations);
            }
        } catch (\Throwable $e) {
            return $fileTranslations;
        }

        return $fileTranslations;
    }
}
