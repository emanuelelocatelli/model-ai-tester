<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ChatTester;
use App\Livewire\ClaudeTester;
use App\Livewire\GeminiTester;
use App\Livewire\PdfTranslator;

Route::get('/', ChatTester::class)->name('home');
Route::get('/claude', ClaudeTester::class)->name('claude');
Route::get('/gemini', GeminiTester::class)->name('gemini');
Route::get('/translate-pdf', PdfTranslator::class)->name('translate-pdf');
