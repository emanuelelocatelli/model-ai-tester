# AI Model Tester - Setup e Utilizzo

## ✅ Installazione Completata

L'applicazione è stata completamente installata e configurata con successo. Tutti i componenti sono pronti all'uso.

## 📁 Struttura del Progetto

```
model-ai-tester/
├── app/Livewire/ChatTester.php          # Componente Livewire principale
├── resources/views/
│   ├── livewire/chat-tester.blade.php   # Vista UI chat
│   └── layouts/app.blade.php            # Layout base
├── routes/web.php                        # Routes configurate
├── scripts/analyze_excel.py              # Script Python per analisi Excel
├── test_sales_data.xlsx                  # File Excel di esempio per test
└── storage/app/temp/                     # Directory per file temporanei
```

## 🔧 Configurazione Richiesta

### 1. OpenAI API Key

Modifica il file `.env` e sostituisci `your-api-key-here` con la tua chiave API OpenAI:

```env
OPENAI_API_KEY=sk-your-actual-api-key-here
```

### 2. Dipendenze Python

Le dipendenze Python sono già installate:
- ✅ pandas (v2.3.3)
- ✅ openpyxl

### 3. Configurazione MAMP PRO

Hai già configurato il virtual host in MAMP PRO. L'applicazione è accessibile su:

```
http://model-ai-tester.local
```

**Verifica della Configurazione MAMP:**

1. Apri MAMP PRO
2. Verifica che il virtual host `model-ai-tester.local` sia configurato
3. Document root deve puntare a: `C:\Users\emanu\GitHub\model-ai-tester\public`
4. Assicurati che i servizi Apache/Nginx e MySQL siano avviati

**Nota:** Il file `.env` è già configurato con `APP_URL=http://model-ai-tester.local`

## 🎯 Funzionalità Implementate

### 1. Chat con AI
- Selezione modelli: GPT-4o, **GPT-4.1**, **GPT-5.1**, GPT-4o Mini, O1 Preview, O1 Mini
- Modello predefinito: **GPT-4.1**
- Cronologia conversazione stateless (in memoria)
- Interfaccia moderna con Tailwind CSS

### 2. Analisi File Excel
- Upload di file .xlsx/.xls
- Analisi automatica con Python/Pandas
- Output dettagliato con:
  - Informazioni generali (righe, colonne)
  - Tipi di dati (df.info())
  - Prime 10 righe (df.head())
  - Statistiche descrittive (df.describe())
  - Valori mancanti
  - Ultimi 5 record (df.tail())

### 3. Estrazione Testo PDF
- Upload di file .pdf
- Estrazione testo completa con smalot/pdfparser (100% PHP, nessun binario richiesto)
- Informazioni sul documento (pagine, caratteri, parole)
- Funziona su qualsiasi sistema operativo senza dipendenze esterne

## 🧪 Test dell'Applicazione

### Test 1: Messaggio Semplice
1. Assicurati che MAMP PRO sia avviato
2. Apri il browser su `http://model-ai-tester.local`
3. Scrivi un messaggio nel campo di testo
4. Premi "Invia" o `Ctrl+Enter`

### Test 2: Analisi Excel
1. Clicca sull'icona di upload (📎)
2. Seleziona `test_sales_data.xlsx`
3. Scrivi: "Analizza questi dati di vendita e dimmi il trend"
4. Invia il messaggio

### Test 3: Analisi PDF
1. Carica un file PDF
2. Scrivi: "Riassumi il contenuto di questo documento"
3. Invia il messaggio

## 📝 Script Python Autonomo

Puoi testare lo script Python indipendentemente:

```bash
python scripts/analyze_excel.py test_sales_data.xlsx
```

## 🛠️ Risoluzione Problemi

### Problema: "Database file does not exist"

**✅ RISOLTO!** L'applicazione è configurata per usare sessioni su file system, non database.

Se vedi ancora questo errore:
```bash
php artisan config:clear
```

### Problema: "Call to undefined function mb_split()" o errore 500

Questo è un problema noto con Laravel 12 e mbstring su Windows/MAMP. Soluzioni:

1. **Riavvia i servizi in MAMP PRO:**
   - Apri MAMP PRO
   - Stop e poi Start dei servizi Apache/Nginx

2. **Verifica mbstring in MAMP:**
   ```bash
   php -m | findstr mbstring
   ```
   Dovrebbe apparire `mbstring` nell'elenco.

3. **Se il problema persiste, usa Laravel 11:**
   ```bash
   composer require laravel/framework:^11.0
   ```

4. **Pulisci la cache di Laravel:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan view:clear
   ```

### Problema: Python non trovato

Assicurati che Python sia nel PATH:
```bash
python --version
```

Se non funziona, modifica `ChatTester.php` alla riga 153 e specifica il path completo di Python.

## 📚 Dipendenze Installate

### PHP (Composer)
- ✅ Laravel 12.38.1
- ✅ Livewire 3.6.4
- ✅ smalot/pdfparser 2.12.1 (estrazione PDF in puro PHP)
- ✅ openai-php/laravel 0.18.0

### Python (pip)
- ✅ pandas 2.3.3
- ✅ openpyxl 3.1.5
- ✅ numpy 2.3.4

## 🎨 Caratteristiche UI

- **Design Moderno:** Gradient colors, ombre, animazioni
- **Responsive:** Funziona su desktop e mobile
- **Avatar Distintivi:** Icone diverse per utente e AI
- **Loading States:** Indicatori di caricamento durante le richieste
- **Error Handling:** Messaggi di errore chiari nell'interfaccia
- **File Preview:** Anteprima dei file caricati prima dell'invio

## 🔒 Sicurezza

- Upload limitati a: `.xlsx`, `.xls`, `.pdf`
- Dimensione massima file: 10MB
- File temporanei eliminati automaticamente dopo l'analisi
- Validazione input con Laravel Validation

## 📖 Documentazione Codice

### Metodi Principali - ChatTester.php

- `sendMessage()`: Gestisce l'invio di messaggi e il flusso completo
- `handleFileUpload()`: Determina il tipo di file e chiama l'analizzatore appropriato
- `analyzeExcelFile()`: Esegue lo script Python per analizzare Excel
- `analyzePdfFile()`: Estrae testo da PDF con spatie
- `callOpenAI()`: Effettua la chiamata API a OpenAI
- `clearChat()`: Resetta la cronologia della chat

## 🚀 Prossimi Passi

1. Configura `OPENAI_API_KEY` nel file `.env`
2. Avvia MAMP PRO (se non è già attivo)
3. Apri il browser su `http://model-ai-tester.local`
4. Inizia a testare l'applicazione!

## 📞 Supporto

Per problemi o domande:
- Controlla i log in `storage/logs/laravel.log`
- Verifica i log MAMP in `C:\MAMP\logs\php_error.log`
- Assicurati che tutte le dipendenze siano installate

---

**Sviluppato con:** Laravel 12, Livewire 3, Tailwind CSS, Python/Pandas

