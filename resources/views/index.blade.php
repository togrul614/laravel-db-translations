@extends(config('db-translations.layout') ?? 'db-translations::layout')

@section('title', trans('db-translations::messages.title'))

@section('content')
    <h1>{{ trans('db-translations::messages.title') }}</h1>

    @if (session('success'))
        <div class="db-translations-alert">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="db-translations-alert db-translations-error">{{ $errors->first() }}</div>
    @endif

    <div class="db-translations-toolbar">
        <form method="GET" action="{{ route(config('db-translations.route_name_prefix') . 'index') }}">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ trans('db-translations::messages.search') }}">
            <select name="group">
                <option value="all">{{ trans('db-translations::messages.all_groups') }}</option>
                @foreach ($groups as $grp)
                    <option value="{{ $grp }}" @selected(request('group') === $grp)>{{ $grp === '*' ? trans('db-translations::messages.json_group') : $grp }}</option>
                @endforeach
            </select>
            <button type="submit">{{ trans('db-translations::messages.filter') }}</button>
        </form>

        <div class="db-translations-actions">
            <form method="POST" action="{{ route(config('db-translations.route_name_prefix') . 'clear_cache') }}">
                @csrf
                <button type="submit">{{ trans('db-translations::messages.clear_cache') }}</button>
            </form>

            <form method="POST" action="{{ route(config('db-translations.route_name_prefix') . 'import') }}"
                onsubmit="return confirm('{{ trans('db-translations::messages.confirm_import') }}');">
                @csrf
                <button type="submit">{{ trans('db-translations::messages.import') }}</button>
            </form>

            @php $createAbility = config('db-translations.abilities.create'); @endphp
            @if (! $createAbility || \Illuminate\Support\Facades\Gate::allows($createAbility))
                <a href="{{ route(config('db-translations.route_name_prefix') . 'create') }}">{{ trans('db-translations::messages.create') }}</a>
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>{{ trans('db-translations::messages.group') }}</th>
                <th>{{ trans('db-translations::messages.key') }}</th>
                <th>{{ trans('db-translations::messages.translations') }}</th>
                <th>{{ trans('db-translations::messages.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($translations as $translation)
                <tr>
                    <td>{{ $translation->id }}</td>
                    <td>{{ $translation->group }}</td>
                    <td>{{ $translation->key }}</td>
                    <td>
                        @foreach ($languages as $lang)
                            <div>
                                <strong>{{ strtoupper($lang->code) }}:</strong>
                                {{ \Illuminate\Support\Str::limit($translation->value[$lang->code] ?? '', 80) ?: trans('db-translations::messages.missing') }}
                            </div>
                        @endforeach
                    </td>
                    <td class="db-translations-actions">
                        <a href="{{ route(config('db-translations.route_name_prefix') . 'edit', $translation) }}">{{ trans('db-translations::messages.edit') }}</a>
                        <form method="POST" action="{{ route(config('db-translations.route_name_prefix') . 'destroy', $translation) }}"
                            onsubmit="return confirm('{{ trans('db-translations::messages.confirm_delete') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit">{{ trans('db-translations::messages.delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">{{ trans('db-translations::messages.empty') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $translations->links() }}
@endsection
