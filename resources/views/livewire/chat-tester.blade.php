<div class="flex flex-col h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    {{-- Controls Bar --}}
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <label for="model-select" class="text-sm font-medium text-gray-700">Modello:</label>
                    <select 
                        id="model-select"
                        wire:model.live="selectedModel" 
                        class="block pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg shadow-sm"
                    >
                        @foreach($availableModels as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-center space-x-2">
                    {{-- Link Traduzione Documenti --}}
                    <a 
                        href="/translate-pdf"
                        class="px-4 py-2 text-sm font-medium text-purple-700 bg-purple-50 rounded-lg hover:bg-purple-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors"
                        title="Traduci documenti PDF, Word, PowerPoint"
                    >
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                        </svg>
                        Traduzione Documenti
                    </a>
                    
                    <button 
                        wire:click="clearChat"
                        type="button"
                        class="px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                        title="Pulisci chat"
                    >
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Area Messaggi --}}
    <div class="flex-1 overflow-y-auto px-4 py-6">
        <div class="max-w-4xl mx-auto space-y-6">
            @forelse($messages as $index => $message)
                <div class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="flex items-start space-x-3 max-w-3xl {{ $message['role'] === 'user' ? 'flex-row-reverse space-x-reverse' : '' }}">
                        {{-- Avatar --}}
                        <div class="flex-shrink-0">
                            @if($message['role'] === 'user')
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Message Content --}}
                        <div class="flex flex-col">
                            <span class="text-xs font-medium text-gray-500 mb-1 {{ $message['role'] === 'user' ? 'text-right' : 'text-left' }}">
                                {{ $message['role'] === 'user' ? 'Tu' : 'AI Assistant' }}
                            </span>
                            <div class="px-4 py-3 rounded-2xl {{ $message['role'] === 'user' ? 'bg-gradient-to-br from-blue-500 to-blue-600 text-white' : 'bg-white text-gray-800 shadow-sm border border-gray-200' }}">
                                <div class="prose prose-sm max-w-none {{ $message['role'] === 'user' ? 'prose-invert' : '' }}">
                                    {!! nl2br(e($message['content'])) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-blue-100 to-purple-100 mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Benvenuto nell'AI Model Tester</h3>
                    <p class="text-gray-500 max-w-md mx-auto">
                        Inizia una conversazione o carica un file Excel/PDF per analizzarlo con l'AI.
                    </p>
                </div>
            @endforelse
            
            {{-- Loading Indicator --}}
            @if($isLoading)
                <div class="flex justify-start">
                    <div class="flex items-start space-x-3 max-w-3xl">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-400 to-pink-500 flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="bg-white px-4 py-3 rounded-2xl shadow-sm border border-gray-200">
                            <div class="flex items-center space-x-2">
                                <div class="flex space-x-1">
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                                <span class="text-sm text-gray-500">AI sta pensando...</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Form Input --}}
    <div class="bg-white border-t border-gray-200 shadow-lg">
        <div class="max-w-4xl mx-auto px-4 py-4">
            <form wire:submit.prevent="sendMessage" class="space-y-3" x-data="{ localPrompt: @entangle('currentPrompt').live }">
                {{-- File Upload Info --}}
                @if($uploadedFile)
                    <div class="flex items-center justify-between px-4 py-2 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-blue-900">{{ $uploadedFile->getClientOriginalName() }}</span>
                            <span class="text-xs text-blue-600">({{ number_format($uploadedFile->getSize() / 1024, 2) }} KB)</span>
                        </div>
                        <button 
                            type="button" 
                            wire:click="$set('uploadedFile', null)" 
                            class="text-blue-600 hover:text-blue-800"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                @endif
                
                {{-- Validation Errors --}}
                @error('currentPrompt')
                    <div class="px-4 py-2 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800">
                        {{ $message }}
                    </div>
                @enderror
                
                @error('uploadedFile')
                    <div class="px-4 py-2 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800">
                        {{ $message }}
                    </div>
                @enderror
                
                {{-- Input Area --}}
                <div class="flex items-end space-x-3">
                    <div class="flex-1">
                        <textarea 
                            x-model="localPrompt"
                            rows="3" 
                            placeholder="Scrivi il tuo messaggio qui... (puoi anche caricare un file Excel o PDF)"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                            @keydown.ctrl.enter="$wire.sendMessage()"
                            :disabled="$wire.isLoading"
                        ></textarea>
                    </div>
                    
                    <div class="flex space-x-2">
                        {{-- File Upload Button --}}
                        <label class="inline-flex items-center justify-center px-4 py-3 border border-gray-300 rounded-xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 cursor-pointer transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                            <input 
                                type="file" 
                                wire:model="uploadedFile" 
                                accept=".xlsx,.xls,.pdf" 
                                class="hidden"
                            >
                        </label>
                        
                        {{-- Send Button --}}
                        <button 
                            type="submit" 
                            class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                            :disabled="$wire.isLoading || !localPrompt.trim()"
                            wire:loading.attr="disabled"
                        >
                            @if($isLoading)
                                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Invio...
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            @endif
                        </button>
                    </div>
                </div>
                
                <p class="text-xs text-gray-500 text-center">
                    Premi <kbd class="px-2 py-1 bg-gray-100 border border-gray-300 rounded text-xs">Ctrl + Enter</kbd> per inviare rapidamente
                </p>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Logging OpenAI API Request in console browser
    window.addEventListener('log-api-request', event => {
        console.group('🚀 OPENAI API REQUEST');
        console.log('Model:', event.detail.model);
        console.log('Messages Count:', event.detail.messagesCount);
        console.log('Temperature:', event.detail.temperature);
        console.log('Has System Prompt:', event.detail.hasSystemPrompt);
        if (event.detail.hasSystemPrompt) {
            console.log('System Prompt Length:', event.detail.systemPromptLength, 'chars');
        }
        console.log('Timestamp:', new Date().toISOString());
        console.groupEnd();
    });

    // Logging OpenAI API Response in console browser
    window.addEventListener('log-api-response', event => {
        console.group('✅ OPENAI API RESPONSE');
        console.log('Model:', event.detail.model);
        console.log('Content Length:', event.detail.contentLength, 'chars');
        console.log('Finish Reason:', event.detail.finishReason);
        console.log('Timestamp:', new Date().toISOString());
        console.groupEnd();
    });
</script>
@endpush
