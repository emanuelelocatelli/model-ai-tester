<div class="flex flex-col h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    {{-- Header --}}
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Traduzione Documenti - DeepL</h1>
                    <p class="text-sm text-gray-500 mt-1">Carica un documento (PDF, Word, PowerPoint) e traducilo nella lingua desiderata</p>
                </div>
                
                <div class="flex items-center space-x-4">
                    {{-- Link alla homepage --}}
                    <a 
                        href="/" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors"
                    >
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Torna alla Home
                    </a>
                    
                    {{-- Pulsante Reset --}}
                    @if($documentFile || $translationCompleted)
                        <button 
                            wire:click="resetForm" 
                            class="px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                            title="Reset form"
                        >
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reset
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Area Principale --}}
    <div class="flex-1 overflow-y-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            
            {{-- Messaggi di Successo --}}
            @if($successMessage)
                <div class="mb-6 px-6 py-4 bg-green-50 border border-green-200 rounded-xl text-green-800 shadow-sm">
                    <div class="flex items-start space-x-3">
                        <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="font-medium">{{ $successMessage }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Messaggi di Errore --}}
            @if($errorMessage)
                <div class="mb-6 px-6 py-4 bg-red-50 border border-red-200 rounded-xl text-red-800 shadow-sm">
                    <div class="flex items-start space-x-3">
                        <svg class="w-6 h-6 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="font-medium">{{ $errorMessage }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Card Principale --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                
                {{-- Header Card --}}
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white">Configura la Traduzione</h2>
                </div>

                {{-- Body Card --}}
                <div class="p-6 space-y-6">
                    
                    {{-- Upload Documento --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Documento da Tradurre
                        </label>
                        
                        @if($documentFile)
                            {{-- File Caricato --}}
                            <div class="flex items-center justify-between px-4 py-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        @php
                                            $extension = strtolower(pathinfo($documentFile->getClientOriginalName(), PATHINFO_EXTENSION));
                                        @endphp
                                        
                                        @if($extension === 'pdf')
                                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                        @elseif($extension === 'docx')
                                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        @elseif($extension === 'pptx')
                                            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                        @else
                                            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-blue-900">{{ $documentFile->getClientOriginalName() }}</p>
                                        <p class="text-xs text-blue-600">{{ number_format($documentFile->getSize() / 1024, 2) }} KB</p>
                                    </div>
                                </div>
                                <button 
                                    type="button" 
                                    wire:click="$set('documentFile', null)" 
                                    class="text-blue-600 hover:text-blue-800 transition-colors"
                                    title="Rimuovi file"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @else
                            {{-- Area Upload --}}
                            <label class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-12 h-12 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-700">
                                        <span class="font-semibold">Clicca per caricare</span> o trascina qui
                                    </p>
                                    <p class="text-xs text-gray-500 mb-3">PDF, Word (DOCX) o PowerPoint (PPTX) - Max 10MB</p>
                                    
                                    {{-- Icone formati supportati --}}
                                    <div class="flex items-center justify-center space-x-4 mt-2">
                                        <div class="text-center">
                                            <svg class="w-6 h-6 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="text-xs text-gray-500">PDF</span>
                                        </div>
                                        <div class="text-center">
                                            <svg class="w-6 h-6 mx-auto text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span class="text-xs text-gray-500">DOCX</span>
                                        </div>
                                        <div class="text-center">
                                            <svg class="w-6 h-6 mx-auto text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="text-xs text-gray-500">PPTX</span>
                                        </div>
                                    </div>
                                </div>
                                <input 
                                    type="file" 
                                    wire:model="documentFile" 
                                    accept=".pdf,.docx,.pptx" 
                                    class="hidden"
                                    :disabled="$wire.isTranslating"
                                >
                            </label>
                        @endif

                        {{-- Errore Validazione --}}
                        @error('documentFile')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        {{-- Loading Upload --}}
                        @if($documentFile)
                            <div wire:loading wire:target="documentFile" class="mt-2 text-sm text-blue-600 flex items-center space-x-2">
                                <svg class="animate-spin h-4 w-4 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Caricamento file in corso...</span>
                            </div>
                        @endif
                    </div>

                    {{-- Selezione Lingua --}}
                    <div>
                        <label for="target-language" class="block text-sm font-medium text-gray-700 mb-2">
                            Lingua di Destinazione
                        </label>
                        <select 
                            id="target-language"
                            wire:model="targetLanguage" 
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            :disabled="$wire.isTranslating"
                        >
                            @foreach($availableLanguages as $code => $name)
                                <option value="{{ $code }}">{{ $name }} ({{ $code }})</option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-xs text-gray-500">
                            Seleziona la lingua in cui vuoi tradurre il documento
                        </p>
                    </div>

                    {{-- Lingua Sorgente (Opzionale) --}}
                    <div>
                        <label for="source-language" class="block text-sm font-medium text-gray-700 mb-2">
                            Lingua Sorgente (opzionale)
                        </label>
                        <select 
                            id="source-language"
                            wire:model="sourceLanguage" 
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            :disabled="$wire.isTranslating"
                        >
                            <option value="">Auto-detect</option>
                            @foreach($availableLanguages as $code => $name)
                                <option value="{{ $code }}">{{ $name }} ({{ $code }})</option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-xs text-gray-500">
                            Lascia su "Auto-detect" per rilevamento automatico della lingua
                        </p>
                    </div>

                    {{-- Pulsante Traduci --}}
                    <div class="pt-4">
                        <button 
                            wire:click="translateDocument" 
                            type="button"
                            class="w-full inline-flex items-center justify-center px-6 py-4 border border-transparent rounded-xl shadow-lg text-base font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all transform hover:scale-105"
                            :disabled="!$wire.documentFile || $wire.isTranslating"
                            wire:loading.attr="disabled"
                        >
                            @if($isTranslating)
                                <svg class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Traduzione in corso...</span>
                            @else
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                                </svg>
                                <span>Traduci Documento</span>
                            @endif
                        </button>
                    </div>

                    {{-- Info Traduzione --}}
                    @if($isTranslating)
                        <div class="px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="text-sm text-yellow-800">
                                    <p class="font-medium">Traduzione in corso...</p>
                                    <p class="mt-1">Questa operazione potrebbe richiedere alcuni minuti. Non chiudere la pagina.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            {{-- Card Download (visibile solo se traduzione completata) --}}
            @if($translationCompleted && $translatedFilePath)
                <div class="mt-6 bg-white rounded-2xl shadow-lg border border-green-300 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                        <h2 class="text-lg font-semibold text-white">Traduzione Completata</h2>
                    </div>
                    
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-base font-semibold text-gray-900">Il tuo documento è pronto!</p>
                                    <p class="text-sm text-gray-500 mt-1">File tradotto disponibile per il download</p>
                                </div>
                            </div>
                            
                            <button 
                                wire:click="downloadTranslatedFile" 
                                class="w-full inline-flex items-center justify-center px-6 py-4 border-2 border-green-700 rounded-xl shadow-lg text-base font-bold text-gray-900 bg-white hover:bg-green-50 focus:outline-none focus:ring-4 focus:ring-green-300 transition-all transform hover:scale-105"
                                style="color: #000000 !important;"
                            >
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Scarica Documento Tradotto
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Info DeepL --}}
            <div class="mt-8 px-6 py-4 bg-blue-50 border border-blue-200 rounded-xl">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium">Informazioni sulla Traduzione</p>
                        <ul class="mt-2 space-y-1 list-disc list-inside">
                            <li>Dimensione massima file: 10 MB</li>
                            <li>Formati supportati: PDF (.pdf), Word (.docx), PowerPoint (.pptx)</li>
                            <li>La traduzione preserva la formattazione originale del documento</li>
                            <li>Servizio di traduzione fornito da DeepL API</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

