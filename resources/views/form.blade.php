@extends(config('db-translations.layout') ?? 'db-translations::layout')

@section('title', $edit ? trans('db-translations::messages.edit') : trans('db-translations::messages.create'))

@section('content')
    <h1>{{ $edit ? trans('db-translations::messages.edit') : trans('db-translations::messages.create') }}</h1>

    <form method="POST" action="{{ $edit ? route(config('db-translations.route_name_prefix') . 'update', $translation) : route(config('db-translations.route_name_prefix') . 'store') }}">
        @csrf
        @if ($edit)
            @method('PUT')
        @endif

        <div class="db-translations-field">
            <label for="group">{{ trans('db-translations::messages.group') }}</label>
            <input type="text" id="group" name="group" list="group_options"
                value="{{ old('group', $edit ? $translation->group : '*') }}" required>
            <datalist id="group_options">
                <option value="*">{{ trans('db-translations::messages.json_group') }}</option>
                @foreach ($groups as $grp)
                    <option value="{{ $grp }}">{{ $grp }}</option>
                @endforeach
            </datalist>
            @error('group')
                <div class="db-translations-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="db-translations-field">
            <label for="key">{{ trans('db-translations::messages.key') }}</label>
            <textarea id="key" name="key" rows="2" required>{{ old('key', $edit ? $translation->key : '') }}</textarea>
            @error('key')
                <div class="db-translations-error">{{ $message }}</div>
            @enderror
        </div>

        @foreach ($languages as $lang)
            <div class="db-translations-field">
                <label for="value_{{ $lang->code }}">{{ $lang->name }} ({{ strtoupper($lang->code) }})</label>
                <textarea id="value_{{ $lang->code }}" name="value[{{ $lang->code }}]" rows="3">{{ old("value.{$lang->code}", $edit ? ($translation->value[$lang->code] ?? '') : '') }}</textarea>
            </div>
        @endforeach

        <div class="db-translations-actions">
            <a href="{{ route(config('db-translations.route_name_prefix') . 'index') }}">{{ trans('db-translations::messages.cancel') }}</a>
            <button type="submit">{{ trans('db-translations::messages.save') }}</button>
        </div>
    </form>
@endsection
