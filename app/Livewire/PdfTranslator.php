<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use App\Services\DeeplService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PdfTranslator extends Component
{
    use WithFileUploads;

    // Proprietà pubbliche per il binding con la vista
    #[Validate('required|file|mimes:pdf,docx,pptx|max:10240')]
    public $documentFile;

    public string $targetLanguage = 'EN-US';
    public ?string $sourceLanguage = null;

    public bool $isTranslating = false;
    public bool $translationCompleted = false;
    public string $translatedFilePath = '';
    public string $translatedFileName = '';
    public string $errorMessage = '';
    public string $successMessage = '';

    // Lingue disponibili
    public array $availableLanguages = [];
    
    // Formati supportati
    public array $supportedFormats = [];

    /**
     * Inizializza il componente
     */
    public function mount(): void
    {
        $this->availableLanguages = $this->getDeeplService()->getSupportedLanguages();
        $this->supportedFormats = config('deepl.supported_formats', []);
    }

    /**
     * Ottiene un'istanza del servizio DeepL
     */
    protected function getDeeplService(): DeeplService
    {
        return new DeeplService();
    }

    /**
     * Metodo principale per tradurre il documento
     */
    public function translateDocument(): void
    {
        // Reset stati precedenti
        $this->resetState();

        // Validazione
        $this->validate();

        if (!$this->documentFile) {
            $this->errorMessage = 'Carica un documento da tradurre (PDF, Word o PowerPoint)';
            return;
        }

        if (empty($this->targetLanguage)) {
            $this->errorMessage = 'Seleziona una lingua di destinazione';
            return;
        }

        // Aumenta timeout e memoria per operazioni pesanti
        set_time_limit(300); // 5 minuti
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '300');

        $this->isTranslating = true;

        Log::info('DocumentTranslator: Inizio traduzione', [
            'fileName' => $this->documentFile->getClientOriginalName(),
            'targetLanguage' => $this->targetLanguage,
            'sourceLanguage' => $this->sourceLanguage ?? 'auto-detect',
        ]);

        try {
            // Salva il file temporaneamente
            $tempPath = $this->documentFile->store('temp', 'local');
            $fullPath = Storage::disk('local')->path($tempPath);

            Log::info('DocumentTranslator: File salvato temporaneamente', ['path' => $fullPath]);

            // Chiama il servizio DeepL per la traduzione
            $result = $this->getDeeplService()->translateDocument(
                $fullPath,
                $this->targetLanguage,
                $this->sourceLanguage
            );

            // Pulisci il file temporaneo
            Storage::disk('local')->delete($tempPath);

            if ($result['success']) {
                // Traduzione completata con successo
                $this->translationCompleted = true;
                $this->translatedFilePath = $result['translated_file_path'];
                $this->translatedFileName = basename($this->translatedFilePath);
                $this->successMessage = 'Traduzione completata con successo! Clicca sul pulsante per scaricare il file tradotto.';

                Log::info('DocumentTranslator: Traduzione completata con successo', [
                    'translatedFile' => $this->translatedFileName
                ]);
            } else {
                // Errore durante la traduzione
                $this->errorMessage = $result['message'] ?? 'Errore durante la traduzione del documento';
                
                Log::error('DocumentTranslator: Errore traduzione', [
                    'message' => $this->errorMessage
                ]);
            }

        } catch (\Exception $e) {
            Log::error('DocumentTranslator: Eccezione durante la traduzione', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            $this->errorMessage = 'Errore: ' . $e->getMessage();
        } finally {
            $this->isTranslating = false;
        }
    }

    /**
     * Download del file tradotto
     */
    public function downloadTranslatedFile()
    {
        if (!$this->translationCompleted || empty($this->translatedFilePath)) {
            $this->errorMessage = 'Nessun file tradotto disponibile per il download';
            return;
        }

        if (!file_exists($this->translatedFilePath)) {
            $this->errorMessage = 'File tradotto non trovato';
            return;
        }

        // Genera un nome file più leggibile usando l'estensione del file tradotto effettivo
        $originalName = $this->documentFile ? $this->documentFile->getClientOriginalName() : 'document';
        $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
        
        // Usa l'estensione del file tradotto (DeepL potrebbe convertire il formato)
        $translatedExtension = pathinfo($this->translatedFilePath, PATHINFO_EXTENSION);
        $downloadName = "{$nameWithoutExt}_translated_{$this->targetLanguage}.{$translatedExtension}";

        return response()->download($this->translatedFilePath, $downloadName)->deleteFileAfterSend(false);
    }

    /**
     * Reset dello stato del componente
     */
    public function resetState(): void
    {
        $this->translationCompleted = false;
        $this->errorMessage = '';
        $this->successMessage = '';
        $this->translatedFilePath = '';
        $this->translatedFileName = '';
    }

    /**
     * Reset completo del form
     */
    public function resetForm(): void
    {
        $this->resetState();
        $this->documentFile = null;
        $this->targetLanguage = 'EN-US';
        $this->sourceLanguage = null;

        // Pulisci anche il file tradotto se esiste
        if (!empty($this->translatedFilePath) && file_exists($this->translatedFilePath)) {
            @unlink($this->translatedFilePath);
        }
    }

    /**
     * Aggiorna il file quando viene caricato
     */
    public function updatedDocumentFile(): void
    {
        $this->resetState();
        $this->validate();
    }

    /**
     * Render del componente
     */
    public function render()
    {
        return view('livewire.pdf-translator')
            ->layout('layouts.app', ['title' => 'Traduzione Documenti - DeepL']);
    }
}

