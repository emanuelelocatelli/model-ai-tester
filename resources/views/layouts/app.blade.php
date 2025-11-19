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
<body class="antialiased bg-gray-50">
    {{-- Navigation Menu --}}
    <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    {{-- Logo/Brand --}}
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                            AI Tools Suite
                        </span>
                    </div>
                    
                    {{-- Navigation Links --}}
                    <div class="hidden sm:ml-8 sm:flex sm:space-x-4">
                        <a href="{{ route('home') }}" 
                           class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('home') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                            OpenAI Tester
                        </a>
                        
                        <a href="{{ route('claude') }}" 
                           class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('claude') ? 'bg-purple-50 text-purple-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Claude Tester
                        </a>
                        
                        <a href="{{ route('gemini') }}" 
                           class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('gemini') ? 'bg-orange-50 text-orange-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                            Gemini Tester
                        </a>
                        
                        <a href="{{ route('translate-pdf') }}" 
                           class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('translate-pdf') ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                            PDF Translator
                        </a>
                    </div>
                </div>
                
                {{-- Mobile menu button --}}
                <div class="flex items-center sm:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        {{-- Mobile menu --}}
        <div x-data="{ mobileMenuOpen: false }" x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" class="sm:hidden border-t border-gray-200">
            <div class="pt-2 pb-3 space-y-1">
                <a href="{{ route('home') }}" 
                   class="block px-4 py-2 text-base font-medium {{ request()->routeIs('home') ? 'bg-blue-50 text-blue-700 border-l-4 border-blue-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    OpenAI Tester
                </a>
                
                <a href="{{ route('claude') }}" 
                   class="block px-4 py-2 text-base font-medium {{ request()->routeIs('claude') ? 'bg-purple-50 text-purple-700 border-l-4 border-purple-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    Claude Tester
                </a>
                
                <a href="{{ route('gemini') }}" 
                   class="block px-4 py-2 text-base font-medium {{ request()->routeIs('gemini') ? 'bg-orange-50 text-orange-700 border-l-4 border-orange-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    Gemini Tester
                </a>
                
                <a href="{{ route('translate-pdf') }}" 
                   class="block px-4 py-2 text-base font-medium {{ request()->routeIs('translate-pdf') ? 'bg-green-50 text-green-700 border-l-4 border-green-700' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                    PDF Translator
                </a>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main>
        {{ $slot }}
    </main>
    
    {{-- Livewire Scripts --}}
    @livewireScripts
    
    {{-- Custom Scripts Stack --}}
    @stack('scripts')
</body>
</html>

