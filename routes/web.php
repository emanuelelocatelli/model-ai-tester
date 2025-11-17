<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ChatTester;
use App\Livewire\PdfTranslator;

Route::get('/', ChatTester::class)->name('home');
Route::get('/translate-pdf', PdfTranslator::class)->name('translate-pdf');
