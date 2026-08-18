@props(['class' => ''])

@php
    $shortLabels = ['uz' => 'UZ', 'uz-Cyrl' => 'ЎЗ', 'ru' => 'RU', 'en' => 'EN'];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-1 ' . $class]) }}>
    @foreach (config('locales.available') as $code => $label)
        <form method="POST" action="{{ route('locale.update', $code) }}">
            @csrf
            <button type="submit" title="{{ $label }}" @class([
                'px-1.5 py-0.5 text-xs rounded',
                'font-semibold text-gray-900 bg-gray-100' => app()->getLocale() === $code,
                'text-gray-400 hover:text-gray-600' => app()->getLocale() !== $code,
            ])>
                {{ $shortLabels[$code] ?? $code }}
            </button>
        </form>
    @endforeach
</div>
