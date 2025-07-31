@props([
    'variant' => 'primary', // primary | secondary | neutral | disabled | delete | edit | back
    'href' => null,
])

@php
    $base = 'inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2';
    $variants = [
        'primary'   => 'bg-orange-600 text-white hover:bg-orange-700 focus:ring-orange-300',
        'secondary' => 'bg-slate-500 text-white hover:bg-slate-700 focus:ring-slate-700',
        'neutral'   => 'bg-gray-200 text-gray-800 hover:bg-gray-300 focus:ring-gray-200',
        'delete'    => 'bg-red-500 text-white hover:bg-red-700 focus:ring-red-300',
        'edit'      => 'bg-yellow-400 text-white hover:bg-yellow-600 focus:ring-yellow-300',
        'disabled'  => 'bg-gray-400 text-gray-700 cursor-not-allowed opacity-50',
        'back'      => 'bg-gray-200 text-gray-800 hover:bg-gray-300 focus:ring-gray-200',
    ];
    $classes = $base.' '.$variants[$variant];
@endphp

@if($variant === 'back' && $href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes, 'disabled' => $variant === 'disabled']) }}>
        {{ $slot }}
    </button>
@endif
