<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use GeminiAPI\Client as GeminiClient;
use GeminiAPI\Resources\Parts\TextPart;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Smalot\PdfParser\Parser as PdfParser;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GeminiTester extends Component
{
    use WithFileUploads;

    // Proprietà pubbliche per il binding con la vista
    public string $selectedModel = 'gemini-3-pro-preview';
    public string $currentPrompt = '';
    
    #[Validate('nullable|file|mimes:xlsx,xls,pdf|max:51200')]
    public $uploadedFile;
    
    public bool $isLoading = false;
    
    // Messages non più proprietà pubblica - troppo grande per serializzazione
    // Salvato in sessione e recuperato on-demand
    protected array $messagesCache = [];
    
    // Modelli disponibili
    public array $availableModels = [
        'gemini-3-pro-preview' => 'Gemini 3 Pro Preview',
        'gemini-2.0-flash-exp' => 'Gemini 2.0 Flash',
        'gemini-1.5-pro' => 'Gemini 1.5 Pro',
        'gemini-1.5-flash' => 'Gemini 1.5 Flash',
    ];
    
    // Getter per messages (recupera da sessione - chiave separata per Gemini)
    public function getMessagesProperty()
    {
        return session('gemini_ui_messages', []);
    }

    /**
     * Metodo principale per inviare messaggi
     */
    public function sendMessage(): void
    {
        // CRITICAL FIX: Salva il file in variabile locale e resetta subito per evitare errori serializzazione Livewire
        $uploadedFileLocal = $this->uploadedFile;
        $this->uploadedFile = null;
        
        // Aumenta timeout e memoria per operazioni pesanti
        set_time_limit(300); // 5 minuti per operazioni API lunghe (maggiore di API timeout)
        ini_set('memory_limit', '512M');
        
        // Validazione: richiede prompt O file
        if (empty($this->currentPrompt) && !$uploadedFileLocal) {
            $this->addError('currentPrompt', 'Inserisci un messaggio o carica un file');
            return;
        }

        if (!empty($this->currentPrompt)) {
            $this->validate([
                'currentPrompt' => 'string|max:5000',
            ]);
        }

        $this->isLoading = true;
        
        Log::info('[GEMINI] sendMessage START', [
            'hasPrompt' => !empty($this->currentPrompt),
            'hasFile' => !empty($uploadedFileLocal),
            'promptLength' => strlen($this->currentPrompt ?? ''),
            'fileName' => $uploadedFileLocal ? $uploadedFileLocal->getClientOriginalName() : null
        ]);
        
        try {
            // Prepara il contenuto del messaggio utente
            $userMessageContent = $this->currentPrompt ?: 'Analizza questo file';
            
            // Se c'è un file caricato, gestiscilo
            $fileAnalysis = '';
            if ($uploadedFileLocal) {
                Log::info('[GEMINI] handleFileUpload START');
                $fileAnalysis = $this->handleFileUpload($uploadedFileLocal);
                
                Log::info('[GEMINI] handleFileUpload END', ['analysisLength' => strlen($fileAnalysis ?? '')]);
                
                if ($fileAnalysis) {
                    // Sanitize UTF-8 per evitare problemi di encoding
                    $fileAnalysis = mb_convert_encoding($fileAnalysis, 'UTF-8', 'UTF-8');
                    
                    // Salva l'analisi del file in SESSIONE (troppo grande per proprietà Livewire)
                    session(['gemini_file_analysis_context' => $fileAnalysis]);
                    
                    $userMessageContent .= "\n\n" . $fileAnalysis;
                    
                    Log::info('[GEMINI] File context salvato in sessione per conversazioni successive');
                }
            }
            
            // Aggiungi il messaggio dell'utente alla cronologia UI (in sessione)
            $uiMessages = session('gemini_ui_messages', []);
            $uiMessages[] = [
                'role' => 'user',
                'content' => $this->currentPrompt ?: '📎 File caricato',
            ];
            
            // Prepara i messaggi per l'AI
            $messagesForAI = $uiMessages;
            if ($fileAnalysis) {
                // Sostituisci l'ultimo messaggio con quello che include l'analisi del file
                $messagesForAI[count($messagesForAI) - 1]['content'] = $userMessageContent;
            }
            
            // Chiama l'API Gemini
            Log::info('[GEMINI] callGemini START');
            $assistantResponse = $this->callGemini($messagesForAI);
            Log::info('[GEMINI] callGemini END', ['responseLength' => strlen($assistantResponse)]);
            
            // Salva messaggio completo in sessione per system prompt
            $fullMessages = session('gemini_full_messages_history', []);
            $fullMessages[] = [
                'role' => 'user',
                'content' => $this->currentPrompt ?: '📎 File caricato'
            ];
            $fullMessages[] = [
                'role' => 'assistant',
                'content' => $assistantResponse  // Versione completa
            ];
            // Limita anche lo storico completo
            if (count($fullMessages) > 20) {
                $fullMessages = array_slice($fullMessages, -20);
            }
            session(['gemini_full_messages_history' => $fullMessages]);
            
            // Aggiungi la risposta alla cronologia UI (versione troncata se necessario)
            $displayContent = $assistantResponse;
            if (strlen($assistantResponse) > 10000) {
                $displayContent = substr($assistantResponse, 0, 10000) . "\n\n... [Risposta troncata per visualizzazione]";
                Log::info('[GEMINI] Risposta AI troncata per UI', [
                    'originalLength' => strlen($assistantResponse),
                    'displayLength' => strlen($displayContent)
                ]);
            }
            
            $uiMessages[] = [
                'role' => 'assistant',
                'content' => $displayContent,
            ];
            
            // Limita numero messaggi UI
            if (count($uiMessages) > 10) {
                $uiMessages = array_slice($uiMessages, -10);
                Log::info('[GEMINI] UI messages limitati a 10');
            }
            
            // Salva in sessione
            session(['gemini_ui_messages' => $uiMessages]);
            
            // IMPORTANTE: Forza Livewire a ricaricare dalla sessione
            $this->dispatch('$refresh');
            
            // Reset del campo di input
            $this->currentPrompt = '';
            
            Log::info('[GEMINI] sendMessage SUCCESS');
            
        } catch (\Exception $e) {
            Log::error('[GEMINI] Errore in sendMessage', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Aggiungi messaggio di errore alla chat
            $uiMessages = session('gemini_ui_messages', []);
            $uiMessages[] = [
                'role' => 'assistant',
                'content' => '❌ Errore: ' . $e->getMessage(),
            ];
            session(['gemini_ui_messages' => $uiMessages]);
        } finally {
            $this->isLoading = false;
            Log::info('[GEMINI] sendMessage END (finally block)');
        }
    }

    /**
     * Gestisce l'upload e l'analisi dei file (Excel o PDF)
     * 
     * @param mixed $uploadedFile File caricato da Livewire
     * @return string|null Risultato dell'analisi o null
     */
    private function handleFileUpload($uploadedFile): ?string
    {
        if (!$uploadedFile) {
            return null;
        }

        try {
            $extension = $uploadedFile->getClientOriginalExtension();
            $fileName = $uploadedFile->getClientOriginalName();
            $fileSize = $uploadedFile->getSize(); // in bytes
            
            // Check dimensione file (max 50MB = 52428800 bytes)
            $maxSize = 52428800; // 50MB
            if ($fileSize > $maxSize) {
                $sizeMB = round($fileSize / 1048576, 2);
                throw new \Exception("File troppo grande ({$sizeMB} MB). Dimensione massima: 50 MB. Per file molto grandi, considera di ridurre le dimensioni o contattare l'amministratore.");
            }
            
            Log::info('[GEMINI] File caricato', [
                'fileName' => $fileName,
                'extension' => $extension,
                'size' => $fileSize,
                'sizeMB' => round($fileSize / 1048576, 2)
            ]);
            
            // Salva il file temporaneamente
            $tempPath = $uploadedFile->store('temp', 'local');
            $fullPath = Storage::disk('local')->path($tempPath);
            
            $analysisResult = '';
            
            if (in_array(strtolower($extension), ['xlsx', 'xls'])) {
                // Analisi Excel con Python/Pandas
                $analysisResult = $this->analyzeExcelFile($fullPath, $fileName);
            } elseif (strtolower($extension) === 'pdf') {
                // Estrazione testo da PDF
                $analysisResult = $this->analyzePdfFile($fullPath, $fileName);
            } else {
                // Pulisci il file temporaneo prima di lanciare l'eccezione
                Storage::disk('local')->delete($tempPath);
                throw new \Exception("Formato file non supportato: .{$extension}. Supportati: Excel (.xlsx, .xls) e PDF (.pdf)");
            }
            
            // Verifica che l'analisi abbia prodotto un risultato
            if (empty($analysisResult)) {
                Storage::disk('local')->delete($tempPath);
                throw new \Exception("L'analisi del file non ha prodotto alcun risultato");
            }
            
            // Pulisci il file temporaneo
            Storage::disk('local')->delete($tempPath);
            
            return $analysisResult;
            
        } catch (\Exception $e) {
            Log::error('[GEMINI] Errore in handleFileUpload: ' . $e->getMessage(), [
                'file' => $fileName ?? 'unknown',
                'extension' => $extension ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            
            // Pulisci il file se esiste
            if (isset($tempPath)) {
                Storage::disk('local')->delete($tempPath);
            }
            
            throw new \Exception("Errore nell'analisi del file: " . $e->getMessage());
        }
    }

    /**
     * Analizza un file Excel usando Python e pandas
     */
    private function analyzeExcelFile(string $filePath, string $fileName): string
    {
        $scriptPath = base_path('scripts/analyze_excel.py');
        
        if (!file_exists($scriptPath)) {
            throw new \Exception("Script Python non trovato: {$scriptPath}");
        }
        
        if (!file_exists($filePath)) {
            throw new \Exception("File Excel non trovato: {$filePath}");
        }
        
        // Normalizza i percorsi per Windows (solo backslash)
        $scriptPath = str_replace('/', '\\', $scriptPath);
        $filePath = str_replace('/', '\\', $filePath);
        
        // Su Windows, usa un batch file wrapper che Apache può eseguire
        // Il batch usa il PATH dell'utente per trovare Python
        $batPath = base_path('scripts/run_python.bat');
        $batPath = str_replace('/', '\\', $batPath);
        
        $pythonCommands = [
            $batPath,  // Batch file wrapper (funziona con PATH utente)
            'python',
            'py'
        ];
        
        $output = null;
        $lastError = '';
        
        foreach ($pythonCommands as $pyCmd) {
            $command = sprintf('%s "%s" "%s" 2>&1', $pyCmd, $scriptPath, $filePath);
            
            Log::info('[GEMINI] === TENTATIVO PYTHON ===', [
                'pythonCmd' => $pyCmd,
                'pythonExists' => file_exists($pyCmd),
                'command' => $command,
                'scriptPath' => $scriptPath,
                'filePath' => $filePath,
                'scriptExists' => file_exists($scriptPath),
                'fileExists' => file_exists($filePath)
            ]);
            
            $result = shell_exec($command);
            
            Log::info('[GEMINI] === RISULTATO PYTHON ===', [
                'pythonCmd' => $pyCmd,
                'resultLength' => $result ? strlen($result) : 0,
                'hasSuccess' => $result && strpos($result, 'ANALISI COMPLETATA') !== false,
                'first100chars' => $result ? substr($result, 0, 100) : 'NULL'
            ]);
            
            if ($result && strpos($result, 'ANALISI COMPLETATA') !== false) {
                $output = $result;
                Log::info('[GEMINI] ✓✓✓ Python eseguito con successo!', [
                    'pythonCommand' => $pyCmd,
                    'outputLength' => strlen($output)
                ]);
                break;
            }
            
            // Salva l'errore per il logging
            if ($result) {
                $lastError = substr($result, 0, 500);
            }
        }
        
        // Verifica l'output
        if ($output === null || empty(trim($output))) {
            $errorMsg = "❌ Impossibile eseguire Python per analizzare l'Excel.\n\n";
            $errorMsg .= "PROBLEMA: Python non è stato trovato o non può essere eseguito dal server web.\n\n";
            $errorMsg .= "SOLUZIONI POSSIBILI:\n";
            $errorMsg .= "1. Installare Python da python.org (opzione 'Add to PATH' + 'Install for all users')\n";
            $errorMsg .= "2. Verificare che MAMP abbia Python installato in C:\\MAMP\\bin\\python\\\n";
            $errorMsg .= "3. Aggiungere Python al PATH di sistema (non solo utente)\n";
            $errorMsg .= "4. Riavviare MAMP dopo l'installazione di Python\n\n";
            $errorMsg .= "Dettagli tecnici: " . ($lastError ?: 'Nessun output da Python');
            
            Log::error('[GEMINI] Fallimento completo Python', [
                'lastError' => $lastError,
                'scriptPath' => $scriptPath,
                'filePath' => $filePath,
                'suggestion' => 'Verificare installazione Python e PATH di sistema'
            ]);
            
            throw new \Exception($errorMsg);
        }
        
        if (strpos($output, 'ANALISI COMPLETATA') === false) {
            // C'è un output ma non contiene il marker di successo
            if (strpos($output, 'Error') !== false || strpos($output, 'Traceback') !== false) {
                Log::error('[GEMINI] Errore Python', ['output' => $output]);
                throw new \Exception("Errore Python: " . substr($output, 0, 500));
            }
            Log::warning('[GEMINI] Output Python incompleto', ['output' => substr($output, 0, 200)]);
        }
        
        Log::info('[GEMINI] Analisi Excel completata con successo!', ['outputLength' => strlen($output)]);
        
        return "--- FILE CARICATO: {$fileName} ---\n\n" . $output;
    }

    /**
     * Analizza un file PDF estraendo il testo
     */
    private function analyzePdfFile(string $filePath, string $fileName): string
    {
        try {
            // Usa smalot/pdfparser per estrarre il testo (funziona su Windows senza binari esterni)
            $parser = new PdfParser();
            $pdf = $parser->parseFile($filePath);
            $pdfText = $pdf->getText();
            
            if (empty(trim($pdfText))) {
                return "--- FILE CARICATO: {$fileName} ---\n\nIl PDF sembra essere vuoto o non contiene testo estraibile.";
            }
            
            // Pulisci il testo da caratteri strani
            $pdfText = preg_replace('/\s+/', ' ', $pdfText);
            $pdfText = trim($pdfText);
            
            $wordCount = str_word_count($pdfText);
            $charCount = mb_strlen($pdfText);
            
            // Ottieni dettagli del PDF
            $details = $pdf->getDetails();
            $pages = $details['Pages'] ?? 'N/A';
            
            return "--- FILE CARICATO: {$fileName} ---\n\n" .
                   "Tipo: Documento PDF\n" .
                   "Pagine: {$pages}\n" .
                   "Caratteri estratti: {$charCount}\n" .
                   "Parole estratte: {$wordCount}\n\n" .
                   "--- CONTENUTO DEL PDF ---\n\n" .
                   $pdfText;
                   
        } catch (\Exception $e) {
            Log::error('[GEMINI] Errore in analyzePdfFile: ' . $e->getMessage());
            throw new \Exception("Errore nell'estrazione del testo dal PDF: " . $e->getMessage());
        }
    }

    /**
     * Chiama l'API Gemini con la cronologia completa
     */
    private function callGemini(array $messages): string
    {
        try {
            // Costruisci system prompt se c'è contesto (file o storico)
            $apiMessages = $messages;
            $fileAnalysisContext = session('gemini_file_analysis_context');
            $fullHistory = session('gemini_full_messages_history', []);
            
            $systemPrompt = '';
            
            if ($fileAnalysisContext || count($fullHistory) > 0) {
                // Aggiungi analisi del file se presente
                if ($fileAnalysisContext) {
                    // Sanitize UTF-8 per evitare errori "Malformed UTF-8 characters"
                    $fileAnalysisContext = mb_convert_encoding($fileAnalysisContext, 'UTF-8', 'UTF-8');
                    
                    $systemPrompt .= "FILE ANALYSIS:\n";
                    $systemPrompt .= "=".str_repeat('=', 79)."\n";
                    $systemPrompt .= $fileAnalysisContext;
                    $systemPrompt .= "\n".str_repeat('=', 80)."\n\n";
                }
                
                // Aggiungi storico conversazione completo
                if (count($fullHistory) > 0) {
                    $systemPrompt .= "CONVERSATION HISTORY:\n";
                    $systemPrompt .= str_repeat('-', 80)."\n";
                    
                    foreach ($fullHistory as $msg) {
                        $label = $msg['role'] === 'user' ? 'User' : 'Assistant';
                        // Sanitize UTF-8 anche per i messaggi storici
                        $content = mb_convert_encoding($msg['content'], 'UTF-8', 'UTF-8');
                        $systemPrompt .= "{$label}: {$content}\n\n";
                    }
                    
                    $systemPrompt .= str_repeat('-', 80)."\n";
                }
                
                Log::info('[GEMINI] System prompt costruito', [
                    'systemPromptLength' => strlen($systemPrompt),
                    'hasFileContext' => !empty($fileAnalysisContext),
                    'historyMessagesCount' => count($fullHistory)
                ]);
            }
            
            // Prepara ultimo messaggio (sanitize UTF-8)
            $lastMessage = end($messages);
            $lastMessage['content'] = mb_convert_encoding($lastMessage['content'], 'UTF-8', 'UTF-8');
            
            // Se c'è un system prompt, lo prependi al messaggio utente
            $finalContent = $systemPrompt ? $systemPrompt . "\n\n" . $lastMessage['content'] : $lastMessage['content'];
            
            // Prepara payload per logging in console browser
            $this->dispatch('log-api-request', 
                model: $this->selectedModel,
                messagesCount: 1,
                temperature: 0.7,
                hasSystemPrompt: !empty($systemPrompt),
                systemPromptLength: strlen($systemPrompt)
            );
            
            // Verifica che la chiave API sia configurata
            $apiKey = config('gemini.api_key');
            if (empty($apiKey)) {
                throw new \Exception("GEMINI_API_KEY non configurata nel file .env");
            }
            
            // Inizializza client Gemini
            $client = new GeminiClient($apiKey);
            
            // Chiamata API Gemini con generativeModel
            $response = $client
                ->generativeModel($this->selectedModel)
                ->generateContent(
                    new TextPart($finalContent)
                );
            
            $content = $response->text() ?? 'Nessuna risposta ricevuta.';
            
            // Log risposta in console browser
            $this->dispatch('log-api-response',
                contentLength: strlen($content),
                finishReason: 'complete',
                model: $this->selectedModel
            );
            
            return $content;
            
        } catch (\Exception $e) {
            Log::error('[GEMINI] Errore in callGemini: ' . $e->getMessage());
            throw new \Exception("Errore API Gemini: " . $e->getMessage());
        }
    }

    /**
     * Cancella la cronologia della chat
     */
    public function clearChat(): void
    {
        $this->currentPrompt = '';
        $this->uploadedFile = null;
        session()->forget('gemini_ui_messages');
        session()->forget('gemini_file_analysis_context');
        session()->forget('gemini_full_messages_history');
    }

    /**
     * Render del componente
     */
    public function render()
    {
        return view('livewire.gemini-tester', [
            'messages' => $this->messages  // Usa il getter
        ])->layout('layouts.app', ['title' => 'Gemini AI Tester']);
    }
}

