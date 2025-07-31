@props(['href', 'active'])

@php
$classes = [
    'flex items-center px-4 py-2 rounded-lg transition',
    $active ? 'bg-orange-50 text-orange-700 font-semibold' : 'text-gray-700 hover:bg-orange-50 hover:text-orange-600'
];
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => implode(' ', $classes)]) }}>
    {{ $slot }}
</a>
