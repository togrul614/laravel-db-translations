<?php

namespace Togrul614\DbTranslations\Support;

use Illuminate\Support\Collection;

class LanguageResolver
{
    /**
     * Resolve the list of languages translations can be entered in, as a
     * collection of objects exposing ->code and ->name.
     */
    public static function all(): Collection
    {
        $source = config('db-translations.languages', []);

        if (is_string($source) && class_exists($source)) {
            $query = $source::query();

            if (method_exists($source, 'scopeActive')) {
                $query->active();
            }

            return $query->get()->map(fn ($row) => (object) [
                'code' => $row->code,
                'name' => $row->name,
            ])->values();
        }

        if (is_array($source)) {
            return collect($source)->map(fn ($row) => (object) $row)->values();
        }

        return collect();
    }

    public static function codes(): array
    {
        return static::all()->pluck('code')->all();
    }
}
