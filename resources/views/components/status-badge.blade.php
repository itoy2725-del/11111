@props(['color' => 'gray', 'text' => ''])

@php
    $colorClasses = [
        'gray' => 'bg-gray-100 text-gray-800 ring-gray-500/10',
        'red' => 'bg-red-100 text-red-800 ring-red-600/10',
        'yellow' => 'bg-yellow-100 text-yellow-800 ring-yellow-600/20',
        'green' => 'bg-green-100 text-green-800 ring-green-600/20',
        'blue' => 'bg-blue-100 text-blue-800 ring-blue-700/10',
        'indigo' => 'bg-indigo-100 text-indigo-800 ring-indigo-700/10',
        'purple' => 'bg-purple-100 text-purple-800 ring-purple-700/10',
        'pink' => 'bg-pink-100 text-pink-800 ring-pink-700/10',
    ];

    $class = $colorClasses[$color] ?? $colorClasses['gray'];
@endphp

<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $class }}">
    {{ $text }}
</span>
