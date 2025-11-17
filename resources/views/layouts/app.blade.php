<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'AI Model Tester' }}</title>
    
    {{-- Vite Assets (include Tailwind CSS) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Livewire Styles --}}
    @livewireStyles
</head>
<body class="antialiased">
    {{ $slot }}
    
    {{-- Livewire Scripts --}}
    @livewireScripts
    
    {{-- Custom Scripts Stack --}}
    @stack('scripts')
</body>
</html>

