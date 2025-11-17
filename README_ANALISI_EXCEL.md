# Analisi File Excel - Guida Utente

## Panoramica

Questa applicazione permette di caricare file Excel e ottenere un'analisi completa e automatica dei dati utilizzando Python e Pandas.

## Funzionalità Analisi

### Analisi Base
- **Informazioni generali**: numero righe, colonne, tipi di dati
- **Prime e ultime righe**: visualizzazione sample dei dati
- **Statistiche descrittive**: media, mediana, deviazione standard, min/max per ogni colonna numerica
- **Valori mancanti**: identificazione e percentuale di dati mancanti

### Analisi Avanzate
- **Correlazioni**: matrice di correlazione tra variabili numeriche, identificazione coppie con alta correlazione (|r| > 0.7)
- **Outlier**: rilevamento automatico valori anomali tramite metodo IQR (Interquartile Range)
- **Duplicati**: identificazione righe duplicate con percentuale e esempi
- **Distribuzione valori**: per colonne categoriche, top 10 valori più frequenti
- **Suggerimenti automatici**: raccomandazioni basate sui dati rilevati

## Requisiti Sistema

### Lato Server (MAMP)
- **Python 3.7+** installato e accessibile dal PATH di sistema
- **Librerie Python richieste**:
  ```bash
  pip install pandas>=1.3.0
  pip install openpyxl>=3.0.0
  pip install numpy>=1.21.0
  ```

### File Supportati
- **Excel**: `.xlsx`, `.xls`
- **PDF**: `.pdf` (solo estrazione testo)
- **Dimensione massima**: 50 MB

## Installazione Python per MAMP

### Opzione 1: Python di Sistema (Raccomandato)

1. **Scarica Python** da [python.org](https://www.python.org/downloads/)
2. **Durante l'installazione**:
   - ✅ Seleziona "Add Python to PATH"
   - ✅ Seleziona "Install for all users"
3. **Installa le librerie**:
   ```bash
   pip install pandas openpyxl numpy
   ```
4. **Riavvia MAMP** completamente

### Opzione 2: Verificare Python MAMP

Se MAMP include Python:
1. Verifica esistenza di `C:\MAMP\bin\python\python.exe`
2. Lo script `run_python.bat` lo userà automaticamente

### Opzione 3: Configurazione Manuale PATH

Se Python è installato ma non funziona:
1. **Pannello di controllo** → Sistema → Impostazioni avanzate
2. **Variabili d'ambiente** → Variabili di sistema → PATH
3. **Aggiungi** il percorso di Python (es: `C:\Python311\`)
4. **Riavvia MAMP**

## Come Usare

1. **Accedi** all'applicazione: `https://model-ai-tester.local`
2. **Seleziona modello AI** dal menu a tendina (default: GPT-4.1)
3. **Carica file Excel**:
   - Click sull'icona 📎
   - Seleziona file `.xlsx` o `.xls` (max 50MB)
4. **Opzionale**: Scrivi una domanda specifica
5. **Invia**: Il sistema analizzerà automaticamente il file
6. **Risultati**: Riceverai analisi completa + risposta AI

## Esempi di Analisi

### File Vendite (esempio)
```
--- INFORMAZIONI GENERALI ---
Numero di righe: 1543
Numero di colonne: 8
Colonne presenti: Data, Prodotto, Quantità, Prezzo, Totale, Categoria, Regione, Venditore

--- CORRELAZIONI TRA VARIABILI NUMERICHE ---
Coppie con alta correlazione (|r| > 0.7):
  - Quantità <-> Totale: 0.892
  - Prezzo <-> Totale: 0.845

--- ANALISI OUTLIER ---
Colonna 'Totale':
  - Numero outlier: 23 (1.49%)
  - Range normale: [50.00, 15000.00]
  - Valori outlier: [25000, 28000, 32000, ...]

--- SUGGERIMENTI AUTOMATICI ---
1. ✓ Il dataset sembra ben strutturato, nessun problema evidente rilevato.
```

## Limitazioni

### Timeout
- **Tempo massimo elaborazione**: 2 minuti (120 secondi)
- File molto grandi potrebbero non completare l'analisi
- Consiglio: file sotto 10 MB per performance ottimali

### Memoria
- **Limite memoria PHP**: 512 MB
- File con milioni di righe potrebbero causare problemi
- Consiglio: pre-filtrare dati se possibile

### Grafici
- **Non supportati** nella versione attuale
- Solo analisi testuali e numeriche
- Grafici pianificati per versioni future

## Troubleshooting

### Errore: "Python non trovato"

**Problema**: Apache/MAMP non trova Python

**Soluzioni**:
1. Verifica installazione Python: `python --version` dal Prompt
2. Reinstalla con "Install for all users" + "Add to PATH"
3. Aggiungi Python al PATH di sistema (non solo utente)
4. Riavvia MAMP dopo modifiche

### Errore: "ModuleNotFoundError: pandas"

**Problema**: Librerie Python non installate

**Soluzione**:
```bash
pip install pandas openpyxl numpy
```

Se hai più versioni Python:
```bash
python -m pip install pandas openpyxl numpy
```

### Errore: "File troppo grande"

**Problema**: File supera 50 MB

**Soluzioni**:
1. Ridurre dimensioni file (rimuovere colonne inutili, filtrare righe)
2. Splitare file in più parti
3. Convertire in formato più leggero (.csv)

### Errore: "Timeout"

**Problema**: Elaborazione supera 2 minuti

**Soluzioni**:
1. Ridurre complessità file
2. Rimuovere colonne non necessarie prima del caricamento
3. Filtrare righe se possibile

### File Excel corrotto

**Problema**: Errore generico durante lettura

**Soluzioni**:
1. Aprire e salvare nuovamente in Excel
2. Verificare che non ci siano fogli protetti da password
3. Esportare in nuovo file `.xlsx`

## Log e Debug

I log dettagliati sono disponibili in:
```
storage/logs/laravel.log
```

Per debug, cerca:
- `TENTATIVO PYTHON` - comandi eseguiti
- `RISULTATO PYTHON` - output ricevuto
- `File caricato` - info sul file uploadato

## Supporto

Per problemi o domande:
1. Controlla i log: `storage/logs/laravel.log`
2. Verifica requisiti Python
3. Consulta sezione Troubleshooting

## Versione

- **Versione corrente**: 1.0
- **Data**: 2025
- **Python richiesto**: 3.7+
- **Pandas richiesto**: 1.3.0+

## Prossime Funzionalità

- [ ] Grafici automatici (matplotlib/seaborn)
- [ ] Export risultati in PDF
- [ ] Analisi predittive (regressione, clustering)
- [ ] Supporto file CSV
- [ ] Confronto tra più file

