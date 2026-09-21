<?php

namespace Globalsoft\DbTranslations\Http\Controllers;

use Globalsoft\DbTranslations\Models\Translation;
use Globalsoft\DbTranslations\Support\LanguageResolver;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class TranslationController extends Controller
{
    protected function authorizeAbility(string $action): void
    {
        $ability = config("db-translations.abilities.{$action}");

        if ($ability !== null) {
            abort_if(Gate::denies($ability), Response::HTTP_FORBIDDEN, '403 Forbidden');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeAbility('index');

        $languages = LanguageResolver::all();

        $query = Translation::query();

        if ($request->filled('group') && $request->group !== 'all') {
            $query->where('group', $request->group);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                    ->orWhere('group', 'like', "%{$search}%")
                    ->orWhere('value', 'like', "%{$search}%");
            });
        }

        $translations = $query->orderByDesc('id')->paginate(15)->withQueryString();

        $groups = Translation::query()->select('group')->distinct()->orderBy('group')->pluck('group');

        return view('db-translations::index', compact('translations', 'languages', 'groups'));
    }

    public function create()
    {
        $this->authorizeAbility('create');

        $languages = LanguageResolver::all();
        $groups = Translation::query()->select('group')->distinct()->orderBy('group')->pluck('group');
        $edit = false;

        return view('db-translations::form', compact('languages', 'groups', 'edit'));
    }

    public function store(Request $request)
    {
        $this->authorizeAbility('create');

        $data = $request->validate([
            'group' => 'required|string|max:100',
            'key' => 'required|string',
            'value' => 'nullable|array',
        ]);

        Translation::query()->updateOrCreate(
            ['group' => $data['group'], 'key' => $data['key']],
            ['value' => $data['value'] ?? []]
        );

        return redirect()
            ->route(config('db-translations.route_name_prefix') . 'index')
            ->with('success', trans('db-translations::messages.created'));
    }

    public function edit(Translation $translation)
    {
        $this->authorizeAbility('edit');

        $languages = LanguageResolver::all();
        $groups = Translation::query()->select('group')->distinct()->orderBy('group')->pluck('group');
        $edit = true;

        return view('db-translations::form', compact('translation', 'languages', 'groups', 'edit'));
    }

    public function update(Request $request, Translation $translation)
    {
        $this->authorizeAbility('edit');

        $data = $request->validate([
            'group' => 'required|string|max:100',
            'key' => 'required|string',
            'value' => 'nullable|array',
        ]);

        $translation->update([
            'group' => $data['group'],
            'key' => $data['key'],
            'value' => $data['value'] ?? [],
        ]);

        return redirect()
            ->route(config('db-translations.route_name_prefix') . 'index')
            ->with('success', trans('db-translations::messages.updated'));
    }

    public function destroy(Translation $translation)
    {
        $this->authorizeAbility('delete');

        $translation->delete();

        return redirect()
            ->route(config('db-translations.route_name_prefix') . 'index')
            ->with('success', trans('db-translations::messages.deleted'));
    }

    public function import()
    {
        $this->authorizeAbility('create');

        try {
            Artisan::call('translations:import');
        } catch (\Throwable $e) {
            return redirect()
                ->route(config('db-translations.route_name_prefix') . 'index')
                ->withErrors($e->getMessage());
        }

        return redirect()
            ->route(config('db-translations.route_name_prefix') . 'index')
            ->with('success', trans('db-translations::messages.imported'));
    }

    public function clearCache()
    {
        $this->authorizeAbility('edit');

        Translation::clearTranslationCache();

        return redirect()
            ->route(config('db-translations.route_name_prefix') . 'index')
            ->with('success', trans('db-translations::messages.cache_cleared'));
    }
}
