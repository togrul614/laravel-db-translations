<?php

namespace Togrul614\DbTranslations;

use Togrul614\DbTranslations\Console\Commands\ImportTranslationsCommand;
use Togrul614\DbTranslations\Services\DatabaseTranslationLoader;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\Translator;

class TranslationsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/db-translations.php', 'db-translations');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'db-translations');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'db-translations');

        if (config('db-translations.register_routes', true)) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        }

        if (config('db-translations.override_translator', true)) {
            $this->overrideTranslator();
        }

        if ($this->app->runningInConsole()) {
            $this->commands([ImportTranslationsCommand::class]);

            $this->publishes([
                __DIR__ . '/../config/db-translations.php' => config_path('db-translations.php'),
            ], 'db-translations-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/db-translations'),
            ], 'db-translations-views');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'db-translations-migrations');

            $this->publishes([
                __DIR__ . '/../resources/lang' => $this->app->langPath('vendor/db-translations'),
            ], 'db-translations-lang');
        }
    }

    protected function overrideTranslator(): void
    {
        $this->app->extend('translation.loader', function ($defaultLoader, $app) {
            $langPath = $app['path.lang'] ?? resource_path('lang');

            return new DatabaseTranslationLoader($app['files'], $langPath);
        });

        $this->app->extend('translator', function ($translator, $app) {
            $translator = new Translator($app['translation.loader'], $app['config']['app.locale']);
            $translator->setFallback($app['config']['app.fallback_locale']);

            return $translator;
        });
    }
}
