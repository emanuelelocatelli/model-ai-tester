# Guida all'utilizzo della Traduzione PDF con DeepL

## Panoramica

È stata implementata una nuova funzionalità per tradurre file PDF utilizzando l'API di DeepL. La funzionalità è accessibile tramite un'interfaccia web dedicata.

## Accesso alla Funzionalità

**URL:** `http://model-ai-tester.local/translate-pdf`

## Caratteristiche Implementate

### 1. **File di Configurazione**
- `config/deepl.php` - Configurazione completa dell'API DeepL
- Supporto per API Free e Pro
- Lista completa delle lingue supportate
- Timeout configurabile (default: 300 secondi)

### 2. **Servizio DeepL** 
- `app/Services/DeeplService.php` - Gestione completa delle API DeepL
- Upload documenti
- Polling stato traduzione
- Download documenti tradotti
- Gestione errori e timeout
- Verifica utilizzo API

### 3. **Componente Livewire**
- `app/Livewire/PdfTranslator.php` - Logica dell'interfaccia
- Upload file PDF (max 10MB)
- Selezione lingua destinazione
- Elaborazione sincrona con timeout esteso
- Download file tradotto

### 4. **Interfaccia Grafica**
- `resources/views/livewire/pdf-translator.blade.php`
- Design coerente con il sistema esistente
- Utilizza Tailwind CSS con gradienti e stile moderno
- Feedback visivo durante l'elaborazione
- Gestione errori chiara

## Come Utilizzare

### Prerequisiti

1. **Verificare la chiave API DeepL**
   ```bash
   # Nel file .env
   DEEPL_API_KEY=610a3da1-dfc0-460a-be6a-e0c1c7204201:fx
   ```

2. **Avviare il server MAMP**
   - Assicurarsi che MAMP sia in esecuzione
   - Verificare che il sito `model-ai-tester.local` sia accessibile

### Procedura di Traduzione

1. **Accedere alla pagina**
   - Aprire il browser e navigare a: `http://model-ai-tester.local/translate-pdf`

2. **Caricare il PDF**
   - Cliccare sull'area di upload o trascinare un file PDF
   - Dimensione massima: 10 MB
   - Solo file PDF sono supportati

3. **Selezionare la lingua**
   - Scegliere la lingua di destinazione dal menu a tendina
   - Opzionalmente, specificare la lingua sorgente (altrimenti auto-detect)

4. **Avviare la traduzione**
   - Cliccare sul pulsante "Traduci Documento"
   - Attendere il completamento (può richiedere alcuni minuti)
   - Non chiudere la pagina durante l'elaborazione

5. **Scaricare il risultato**
   - Una volta completata, apparirà una card verde con il pulsante di download
   - Cliccare "Scarica PDF Tradotto" per ottenere il file

## Lingue Supportate

### Principali Lingue Disponibili:
- **Italiano** (IT)
- **Inglese** (EN-US, EN-GB)
- **Tedesco** (DE)
- **Francese** (FR)
- **Spagnolo** (ES)
- **Portoghese** (PT-BR, PT-PT)
- **Olandese** (NL)
- **Polacco** (PL)
- **Russo** (RU)
- **Giapponese** (JA)
- **Cinese** (ZH)
- **Coreano** (KO)
- E molte altre...

## Gestione Timeout

Il sistema è configurato per gestire traduzioni che richiedono fino a 5 minuti:

- **Timeout PHP**: 300 secondi
- **Timeout API DeepL**: 300 secondi
- **Polling**: Controllo stato ogni 5 secondi
- **Massimo tentativi**: 60 (5 minuti totali)

Se la traduzione supera questo tempo, verrà generato un errore di timeout.

## Risoluzione Problemi

### Problema: "API Key DeepL non configurata"
**Soluzione:** Verificare che `DEEPL_API_KEY` sia presente nel file `.env`

### Problema: "Timeout: la traduzione sta impiegando troppo tempo"
**Soluzione:** 
- Il documento è troppo grande o complesso
- Riprovare con un file più piccolo
- Verificare la connessione internet

### Problema: "Errore durante l'upload del documento"
**Soluzione:**
- Verificare che il file sia un PDF valido
- Controllare che le dimensioni siano inferiori a 10 MB
- Verificare la connessione API DeepL

### Problema: Pagina non accessibile (errore 302)
**Soluzione:**
- Verificare che MAMP sia in esecuzione
- Controllare la configurazione del virtual host in MAMP Pro
- Verificare che l'URL `model-ai-tester.local` sia correttamente configurato

## Test Manuale Rapido

### Test 1: Verifica Route
```bash
php artisan route:list | Select-String -Pattern "translate-pdf"
```
Dovrebbe mostrare la route registrata.

### Test 2: Accesso Pagina
1. Aprire browser
2. Navigare a `http://model-ai-tester.local/translate-pdf`
3. Dovrebbe apparire l'interfaccia di traduzione

### Test 3: Traduzione PDF
1. Procurarsi un PDF di test (anche di poche pagine)
2. Seguire la procedura sopra descritta
3. Verificare il download del file tradotto

## Struttura dei File

```
.
├── config/
│   └── deepl.php                           # Configurazione API
├── app/
│   ├── Services/
│   │   └── DeeplService.php                # Servizio API DeepL
│   └── Livewire/
│       └── PdfTranslator.php               # Componente Livewire
├── resources/
│   └── views/
│       └── livewire/
│           └── pdf-translator.blade.php    # Vista interfaccia
├── routes/
│   └── web.php                             # Route (GET /translate-pdf)
└── storage/
    └── app/
        ├── temp/                            # File temporanei upload
        └── translations/                    # File tradotti
```

## Note Tecniche

### Implementazione API
- Le chiamate API sono effettuate tramite Guzzle HTTP Client (già presente in Laravel)
- Non è stato necessario installare il SDK ufficiale DeepL
- Implementazione diretta delle REST API DeepL v2

### Flusso di Traduzione
1. Upload del file PDF a DeepL (`POST /v2/document`)
2. Ricezione `document_id`
3. Polling dello stato (`POST /v2/document/{document_id}`)
4. Stati: `queued` → `translating` → `done`
5. Download del documento tradotto (`POST /v2/document/{document_id}/result`)

### Sicurezza
- File temporanei vengono eliminati dopo l'elaborazione
- I file tradotti sono salvati in `storage/app/translations/`
- Solo file PDF accettati
- Validazione dimensione file (max 10MB)

## Supporto

Per problemi o domande sull'implementazione, fare riferimento ai log di Laravel:
```
C:\MAMP\logs\
storage/logs/laravel.log
```

---

**Implementazione completata il:** 17 Novembre 2025  
**Versione Laravel:** 12.x  
**Versione Livewire:** 3.6  
**API DeepL:** v2

