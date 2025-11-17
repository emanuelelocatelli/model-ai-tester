# Installazione Python per MAMP - Guida Completa

## Step 1: Download Python

1. Vai su: https://www.python.org/downloads/
2. Clicca sul pulsante giallo "Download Python 3.x.x" (versione più recente)
3. Salva il file (es: `python-3.12.x-amd64.exe`)

## Step 2: Installazione (CRITICO)

1. **Esegui** il file scaricato come Amministratore (tasto destro → Esegui come amministratore)
2. **IMPORTANTE - PRIMA SCHERMATA**:
   
   ✅ **SELEZIONA QUESTE 2 OPZIONI**:
   - [ ] Use admin privileges when installing py.exe
   - [X] **Add python.exe to PATH** ← FONDAMENTALE!
   - [X] **Install launcher for all users (recommended)** ← FONDAMENTALE!

3. Clicca su **"Customize installation"** (NON "Install Now")

4. **Optional Features** - Seleziona tutto:
   - [X] Documentation
   - [X] pip
   - [X] tcl/tk and IDLE
   - [X] Python test suite
   - [X] py launcher
   - [X] for all users (requires admin privileges)

5. Clicca **Next**

6. **Advanced Options**:
   - [X] Install Python for all users
   - [X] Associate files with Python
   - [X] Create shortcuts for installed applications
   - [X] Add Python to environment variables ← CRITICO!
   - [X] Precompile standard library
   - [ ] Download debugging symbols (opzionale)
   - [ ] Download debug binaries (opzionale)
   
   **Percorso installazione**: Usa quello suggerito (es: `C:\Program Files\Python312\`)

7. Clicca **Install**

8. Attendi il completamento

9. Clicca **Close**

## Step 3: Verifica Installazione

Apri **Prompt dei comandi** (NON PowerShell per questo test):

```bash
python --version
```

Dovresti vedere: `Python 3.12.x` (o la versione installata)

```bash
pip --version
```

Dovresti vedere: `pip 24.x.x from C:\Program Files\Python312\...`

## Step 4: Installa Librerie Python

Nel Prompt dei comandi:

```bash
pip install pandas openpyxl numpy
```

Attendi che scarichi e installi tutto (circa 1-2 minuti).

Verifica installazione:

```bash
python -c "import pandas; print('Pandas OK:', pandas.__version__)"
python -c "import openpyxl; print('Openpyxl OK:', openpyxl.__version__)"
python -c "import numpy; print('Numpy OK:', numpy.__version__)"
```

Dovresti vedere:
```
Pandas OK: 2.x.x
Openpyxl OK: 3.x.x
Numpy OK: 1.x.x
```

## Step 5: Riavvia MAMP

1. Apri **MAMP**
2. Clicca **Stop Servers**
3. Attendi che si fermino completamente
4. Clicca **Start Servers**
5. Verifica che Apache e MySQL siano verdi

## Step 6: Test Finale

Torna sul progetto e prova a caricare un file Excel!

---

## Problemi Comuni

### "python non è riconosciuto come comando"

**Causa**: PATH non aggiornato

**Soluzione**:
1. Chiudi TUTTI i Prompt/PowerShell aperti
2. Riapri un NUOVO Prompt dei comandi
3. Riprova `python --version`
4. Se ancora non funziona:
   - Pannello di controllo → Sistema → Impostazioni avanzate di sistema
   - Variabili d'ambiente → PATH (variabili di sistema)
   - Verifica presenza: `C:\Program Files\Python312\` e `C:\Program Files\Python312\Scripts\`
   - Riavvia il computer

### "pip non è riconosciuto come comando"

**Soluzione**:
```bash
python -m pip install pandas openpyxl numpy
```

### "Permission denied" durante pip install

**Soluzione**: Esegui Prompt dei comandi come Amministratore

---

## Dopo l'Installazione

Una volta completati tutti gli step, torna sull'applicazione:

1. Apri https://model-ai-tester.local
2. Carica un file Excel
3. Il sistema ora dovrebbe analizzarlo correttamente!

---

**Tempo totale stimato**: 5-10 minuti
**Spazio richiesto**: ~100 MB

Se hai problemi, mandami uno screenshot dell'errore e ti aiuto subito!

