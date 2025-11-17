<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeeplService
{
    protected Client $httpClient;
    protected string $apiKey;
    protected string $apiUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->apiKey = config('deepl.api_key');
        $this->apiUrl = config('deepl.api_url');
        $this->timeout = config('deepl.timeout', 300);

        $this->httpClient = new Client([
            'timeout' => $this->timeout,
            'verify' => false, // Per evitare problemi SSL in ambiente locale MAMP
        ]);
    }

    /**
     * Ottiene la lista delle lingue supportate da DeepL
     */
    public function getSupportedLanguages(): array
    {
        return config('deepl.target_languages', []);
    }

    /**
     * Traduce un documento PDF
     * 
     * @param string $filePath Percorso completo del file PDF da tradurre
     * @param string $targetLanguage Codice lingua di destinazione (es: 'IT', 'EN-US')
     * @param string|null $sourceLanguage Codice lingua sorgente (null per auto-detect)
     * @return array Array con 'success', 'message', 'translated_file_path'
     */
    public function translateDocument(string $filePath, string $targetLanguage, ?string $sourceLanguage = null): array
    {
        try {
            Log::info('DeepL: Inizio traduzione documento', [
                'file' => $filePath,
                'target_lang' => $targetLanguage,
                'source_lang' => $sourceLanguage ?? 'auto-detect'
            ]);

            // Verifica che il file esista
            if (!file_exists($filePath)) {
                throw new \Exception("File non trovato: {$filePath}");
            }

            // Verifica che l'API key sia configurata
            if (empty($this->apiKey)) {
                throw new \Exception("API Key DeepL non configurata. Verifica la variabile DEEPL_API_KEY nel file .env");
            }

            // Step 1: Upload del documento per la traduzione
            Log::info('DeepL: Upload documento in corso...');
            $uploadResult = $this->uploadDocumentForTranslation($filePath, $targetLanguage, $sourceLanguage);
            
            if (!$uploadResult || !isset($uploadResult['document_id']) || !isset($uploadResult['document_key'])) {
                throw new \Exception("Errore durante l'upload del documento a DeepL");
            }

            $documentId = $uploadResult['document_id'];
            $documentKey = $uploadResult['document_key'];

            Log::info('DeepL: Documento caricato', ['document_id' => $documentId]);

            // Step 2: Polling dello stato della traduzione
            Log::info('DeepL: Attendo completamento traduzione...');
            $translationStatus = $this->waitForTranslationCompletion($documentId, $documentKey);

            if ($translationStatus !== 'done') {
                throw new \Exception("Traduzione non completata. Stato: {$translationStatus}");
            }

            Log::info('DeepL: Traduzione completata, download in corso...');

            // Step 3: Download del documento tradotto
            $translatedFilePath = $this->downloadTranslatedDocument($documentId, $documentKey);

            if (!$translatedFilePath) {
                throw new \Exception("Errore durante il download del documento tradotto");
            }

            Log::info('DeepL: Traduzione completata con successo', [
                'translated_file' => $translatedFilePath
            ]);

            return [
                'success' => true,
                'message' => 'Documento tradotto con successo',
                'translated_file_path' => $translatedFilePath,
                'document_id' => $documentId,
            ];

        } catch (\Exception $e) {
            Log::error('DeepL: Errore durante la traduzione', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'translated_file_path' => null,
            ];
        }
    }

    /**
     * Upload del documento per la traduzione
     */
    protected function uploadDocumentForTranslation(string $filePath, string $targetLanguage, ?string $sourceLanguage): ?array
    {
        try {
            $multipart = [
                [
                    'name' => 'target_lang',
                    'contents' => $targetLanguage,
                ],
                [
                    'name' => 'file',
                    'contents' => fopen($filePath, 'r'),
                    'filename' => basename($filePath),
                ],
            ];

            // Aggiungi lingua sorgente se specificata
            if ($sourceLanguage) {
                $multipart[] = [
                    'name' => 'source_lang',
                    'contents' => $sourceLanguage,
                ];
            }

            $response = $this->httpClient->post("{$this->apiUrl}/document", [
                'headers' => [
                    'Authorization' => "DeepL-Auth-Key {$this->apiKey}",
                ],
                'multipart' => $multipart,
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            Log::info('DeepL: Risposta upload', $responseData);

            // Ritorna sia document_id che document_key
            if (isset($responseData['document_id']) && isset($responseData['document_key'])) {
                return [
                    'document_id' => $responseData['document_id'],
                    'document_key' => $responseData['document_key'],
                ];
            }

            return null;

        } catch (GuzzleException $e) {
            Log::error('DeepL: Errore upload documento', [
                'message' => $e->getMessage(),
                'response' => method_exists($e, 'getResponse') && $e->getResponse() 
                    ? $e->getResponse()->getBody()->getContents() 
                    : null
            ]);
            throw new \Exception("Errore durante l'upload del documento: " . $e->getMessage());
        }
    }

    /**
     * Attende il completamento della traduzione con polling
     */
    protected function waitForTranslationCompletion(string $documentId, string $documentKey, int $maxAttempts = 60): string
    {
        $attempt = 0;
        $delay = 5; // Secondi tra ogni check

        while ($attempt < $maxAttempts) {
            try {
                $response = $this->httpClient->post("{$this->apiUrl}/document/{$documentId}", [
                    'headers' => [
                        'Authorization' => "DeepL-Auth-Key {$this->apiKey}",
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ],
                    'form_params' => [
                        'document_key' => $documentKey,
                    ],
                ]);

                $responseData = json_decode($response->getBody()->getContents(), true);
                $status = $responseData['status'] ?? 'unknown';

                Log::info('DeepL: Stato traduzione', [
                    'document_id' => $documentId,
                    'status' => $status,
                    'attempt' => $attempt + 1,
                    'seconds_billed' => $responseData['seconds_remaining'] ?? null,
                ]);

                // Stati possibili: 'queued', 'translating', 'done', 'error'
                if ($status === 'done') {
                    return 'done';
                }

                if ($status === 'error') {
                    throw new \Exception("Errore durante la traduzione del documento");
                }

                // Attendi prima del prossimo tentativo
                sleep($delay);
                $attempt++;

            } catch (GuzzleException $e) {
                Log::error('DeepL: Errore check stato', [
                    'message' => $e->getMessage(),
                ]);
                throw new \Exception("Errore durante il controllo dello stato: " . $e->getMessage());
            }
        }

        throw new \Exception("Timeout: la traduzione sta impiegando troppo tempo");
    }

    /**
     * Download del documento tradotto
     */
    protected function downloadTranslatedDocument(string $documentId, string $documentKey): ?string
    {
        try {
            $response = $this->httpClient->post("{$this->apiUrl}/document/{$documentId}/result", [
                'headers' => [
                    'Authorization' => "DeepL-Auth-Key {$this->apiKey}",
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'document_key' => $documentKey,
                ],
            ]);

            // Salva il file tradotto in storage/app/translations
            $translatedContent = $response->getBody()->getContents();
            $fileName = 'translated_' . uniqid() . '.pdf';
            $storagePath = 'translations/' . $fileName;

            Storage::disk('local')->put($storagePath, $translatedContent);

            $fullPath = Storage::disk('local')->path($storagePath);

            Log::info('DeepL: Documento scaricato', [
                'document_id' => $documentId,
                'path' => $fullPath,
                'size' => strlen($translatedContent),
            ]);

            return $fullPath;

        } catch (GuzzleException $e) {
            Log::error('DeepL: Errore download documento', [
                'message' => $e->getMessage(),
            ]);
            throw new \Exception("Errore durante il download del documento: " . $e->getMessage());
        }
    }

    /**
     * Verifica l'utilizzo dell'API (quote rimanenti)
     */
    public function getUsageStatistics(): array
    {
        try {
            $response = $this->httpClient->get("{$this->apiUrl}/usage", [
                'headers' => [
                    'Authorization' => "DeepL-Auth-Key {$this->apiKey}",
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'character_count' => $data['character_count'] ?? 0,
                'character_limit' => $data['character_limit'] ?? 0,
            ];

        } catch (GuzzleException $e) {
            Log::error('DeepL: Errore verifica utilizzo', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}

