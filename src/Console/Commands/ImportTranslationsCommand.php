<?php

namespace Globalsoft\DbTranslations\Console\Commands;

use Globalsoft\DbTranslations\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class ImportTranslationsCommand extends Command
{
    protected $signature = 'translations:import';

    protected $description = 'Import lang/*.php and lang/*.json files into the database translations table';

    public function handle(): int
    {
        $langPath = config('db-translations.import_path') ?: resource_path('lang');

        if (! File::isDirectory($langPath)) {
            $langPath = base_path('lang');
        }

        if (! File::isDirectory($langPath)) {
            $this->error("Lang directory not found at {$langPath}");

            return self::FAILURE;
        }

        $this->info('Scanning language files...');

        $collected = [];

        foreach (File::directories($langPath) as $dir) {
            $locale = basename($dir);

            if ($locale === 'vendor') {
                continue;
            }

            foreach (File::files($dir) as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $group = $file->getFilenameWithoutExtension();

                try {
                    $translations = include $file->getRealPath();

                    if (is_array($translations)) {
                        foreach (Arr::dot($translations) as $key => $value) {
                            if (is_string($value) || is_numeric($value)) {
                                $collected[$group][$key][$locale] = (string) $value;
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    $this->warn("Could not parse file {$file->getFilename()}: " . $e->getMessage());
                }
            }
        }

        foreach (File::files($langPath) as $file) {
            if ($file->getExtension() !== 'json') {
                continue;
            }

            $locale = $file->getFilenameWithoutExtension();
            $content = json_decode(File::get($file->getRealPath()), true);

            if (is_array($content)) {
                foreach ($content as $key => $value) {
                    if (is_string($value) || is_numeric($value)) {
                        $collected['*'][$key][$locale] = (string) $value;
                    }
                }
            }
        }

        $this->info('Saving to database...');

        $count = 0;

        foreach ($collected as $group => $keys) {
            foreach ($keys as $key => $localeValues) {
                $translation = Translation::query()->firstOrNew(['group' => $group, 'key' => $key]);
                $translation->value = array_merge($localeValues, $translation->value ?? []);
                $translation->save();
                $count++;
            }
        }

        Translation::clearTranslationCache();

        $this->info("Successfully imported {$count} translation keys into the database!");

        return self::SUCCESS;
    }
}
