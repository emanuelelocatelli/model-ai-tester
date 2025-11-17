# Installazione Python Parallelo per MAMP

## 🎯 Obiettivo
Installare una **seconda versione** di Python accessibile da MAMP, senza toccare quella esistente.

---

## 📦 Situazione

### Prima (Attuale)
```
Python 3.11.9 (Microsoft Store)
├─ Posizione: C:\Users\emanu\AppData\...
├─ Accessibile da: Utente 'emanu' ✓
├─ Accessibile da: Apache/MAMP ✗
└─ Stato: Rimane installato
```

### Dopo (Con la nuova installazione)
```
Python 3.11.9 (Microsoft Store)
├─ Posizione: C:\Users\emanu\AppData\...
└─ Uso: Tue applicazioni personali

Python 3.12.x (Ufficiale)
├─ Posizione: C:\Python312\
├─ Accessibile da: Tutti gli utenti ✓
├─ Accessibile da: Apache/MAMP ✓
└─ Uso: Server web MAMP
```

---

## 🚀 Installazione Step-by-Step

### Step 1: Download Python

1. Vai su: **https://www.python.org/downloads/**
2. Clicca sul pulsante giallo **"Download Python 3.12.x"**
3. Salva il file (es: `python-3.12.7-amd64.exe`)

---

### Step 2: Esegui Installer

1. **Esegui come Amministratore**:
   - Tasto destro sul file scaricato
   - Seleziona "Esegui come amministratore"

---

### Step 3: Configurazione Installazione

#### ⚠️ SCHERMATA 1 - IMPORTANTE

```
┌────────────────────────────────────────────────┐
│  Install Python 3.12.7                         │
├────────────────────────────────────────────────┤
│                                                │
│  [X] Use admin privileges when installing py   │
│  [X] Add python.exe to PATH                    │
│                                                │
│  ┌──────────────────────────────────────────┐ │
│  │  Customize installation            ← CLICK│ │
│  └──────────────────────────────────────────┘ │
│                                                │
│  [ Install Now ]            ← NON cliccare    │
│                                                │
└────────────────────────────────────────────────┘
```

**✅ Spunta**: "Use admin privileges" + "Add python.exe to PATH"  
**✅ Clicca**: "Customize installation"

---

#### SCHERMATA 2 - Optional Features

```
┌────────────────────────────────────────────────┐
│  Optional Features                             │
├────────────────────────────────────────────────┤
│                                                │
│  [X] Documentation                             │
│  [X] pip                                       │
│  [X] tcl/tk and IDLE                           │
│  [X] Python test suite                         │
│  [X] py launcher                               │
│  [X] for all users (requires elevation)        │
│                                                │
│  ┌──────────────────────────────────────────┐ │
│  │  Next                                     │ │
│  └──────────────────────────────────────────┘ │
└────────────────────────────────────────────────┘
```

**✅ Seleziona tutto**  
**✅ Clicca**: "Next"

---

#### ⚠️ SCHERMATA 3 - Advanced Options (CRITICA!)

```
┌────────────────────────────────────────────────┐
│  Advanced Options                              │
├────────────────────────────────────────────────┤
│                                                │
│  [X] Install Python for all users   ← CRITICO │
│  [X] Associate files with Python (.py)         │
│  [X] Create shortcuts                          │
│  [X] Add Python to environment variables       │
│  [X] Precompile standard library               │
│  [ ] Download debugging symbols                │
│  [ ] Download debug binaries                   │
│                                                │
│  Customize install location:                   │
│  ┌──────────────────────────────────────────┐ │
│  │ C:\Python312\              ← CAMBIA QUI  │ │
│  └──────────────────────────────────────────┘ │
│                                                │
│  ┌──────────────────────────────────────────┐ │
│  │  Install                                  │ │
│  └──────────────────────────────────────────┘ │
└────────────────────────────────────────────────┘
```

**✅ IMPORTANTE - Percorso**:
- **Cambia da**: `C:\Program Files\Python312\`
- **A**: `C:\Python312\`

**Perché `C:\Python312\` invece di `C:\Program Files\`?**
- Percorso più corto e semplice
- Nessun problema con spazi nel nome
- Più facile da gestire per Apache
- Pratica comune per server

**✅ Spunta**: 
- "Install Python for all users"
- "Add Python to environment variables"

**✅ Clicca**: "Install"

---

### Step 4: Attendi Installazione

```
Setup is installing Python 3.12.7 (64-bit)...
[████████████████████████░░░░░░] 75%
```

Tempo: 2-3 minuti

---

### Step 5: Verifica Installazione

Apri **Prompt dei comandi** (normale, non serve Amministratore):

```bash
C:\Python312\python.exe --version
```

**Output atteso**:
```
Python 3.12.7
```

Se vedi questo, l'installazione è riuscita! ✓

---

### Step 6: Installa Librerie per MAMP

Apri **Prompt dei comandi come Amministratore**:

```bash
C:\Python312\python.exe -m pip install pandas openpyxl numpy
```

**Output atteso**:
```
Collecting pandas
  Downloading pandas-2.1.4-...
Collecting openpyxl
  Downloading openpyxl-3.1.2-...
Collecting numpy
  Downloading numpy-1.26.3-...
...
Successfully installed pandas-2.1.4 openpyxl-3.1.2 numpy-1.26.3
```

---

### Step 7: Verifica Librerie

```bash
C:\Python312\python.exe -c "import pandas; print('Pandas:', pandas.__version__)"
C:\Python312\python.exe -c "import openpyxl; print('Openpyxl:', openpyxl.__version__)"
C:\Python312\python.exe -c "import numpy; print('Numpy:', numpy.__version__)"
```

**Output atteso**:
```
Pandas: 2.1.4
Openpyxl: 3.1.2
Numpy: 1.26.3
```

Tutto OK! ✓

---

### Step 8: Riavvia MAMP

1. Apri **MAMP**
2. Clicca **"Stop Servers"**
3. Attendi che Apache e MySQL si fermino (indicatori rossi)
4. Clicca **"Start Servers"**
5. Attendi che diventino verdi

---

### Step 9: Test Finale

1. Apri browser
2. Vai su: **https://model-ai-tester.local**
3. **Carica un file Excel**
4. Clicca "Invia"
5. Attendi...

**Risultato atteso**: Analisi completa del file con correlazioni, outlier, ecc. ✓

---

## ✅ Checklist Completa

- [ ] Scaricato Python 3.12.x da python.org
- [ ] Eseguito installer come Amministratore
- [ ] Selezionato "Customize installation"
- [ ] Selezionato tutte le Optional Features
- [ ] Cambiato percorso in `C:\Python312\`
- [ ] Spuntato "Install for all users"
- [ ] Installazione completata
- [ ] Verificato versione: `C:\Python312\python.exe --version`
- [ ] Installato pandas, openpyxl, numpy
- [ ] Verificato librerie funzionanti
- [ ] Riavviato MAMP
- [ ] Testato upload Excel dall'app web
- [ ] Ricevuto analisi completa ✓

---

## 🔍 Risoluzione Problemi

### Problema 1: "python is not recognized"

**Causa**: Percorso sbagliato o installazione non riuscita

**Soluzione**:
```bash
# Usa il percorso completo
C:\Python312\python.exe --version
```

### Problema 2: "pip is not recognized"

**Causa**: pip non trovato nel PATH

**Soluzione**:
```bash
# Usa il modulo pip
C:\Python312\python.exe -m pip install pandas openpyxl numpy
```

### Problema 3: "Permission denied" durante pip install

**Causa**: Permessi insufficienti

**Soluzione**:
- Chiudi il Prompt
- Apri **Prompt dei comandi come Amministratore**
- Riprova il comando

### Problema 4: MAMP ancora non trova Python

**Causa**: MAMP non riavviato o cache

**Soluzione**:
1. Stop Servers in MAMP
2. Chiudi completamente MAMP
3. Riapri MAMP
4. Start Servers
5. Pulisci cache Laravel: `php artisan optimize:clear`
6. Riprova

### Problema 5: "ModuleNotFoundError: No module named 'pandas'"

**Causa**: Librerie installate per Python sbagliato

**Soluzione**:
```bash
# Verifica quale Python stai usando
where python

# Installa librerie per quello specifico
C:\Python312\python.exe -m pip install pandas openpyxl numpy
```

---

## 🎓 Dopo l'Installazione

### Quale Python Usa Cosa?

**Terminale/PowerShell**:
```bash
python --version
# Potrebbe usare: Python 3.11.9 (Microsoft Store)
```

**MAMP/Apache** (via batch file):
```
Cerca in ordine:
1. C:\Python312\python.exe  ← NUOVO (userà questo!)
2. C:\Python311\python.exe
3. C:\Program Files\Python312\python.exe
4. C:\MAMP\bin\python\python.exe
5. Microsoft Store (fallback)
```

**Risultato**: MAMP userà automaticamente la nuova versione! ✓

### Come Verificare Quale Python Usa MAMP

Controlla i log Laravel dopo un upload Excel:

```bash
Get-Content storage/logs/laravel.log | Select-Object -Last 50 | Select-String "Python"
```

Se vedi percorsi tipo `C:\Python312\...`, funziona! ✓

---

## 📊 Riepilogo

| Aspetto | Vecchia Situazione | Nuova Situazione |
|---------|-------------------|------------------|
| Python per utente | 3.11.9 (Store) ✓ | 3.11.9 (Store) ✓ |
| Python per MAMP | ✗ Non trovato | 3.12.x ✓ |
| Librerie pandas | Solo per 3.11.9 | Per entrambi ✓ |
| Analisi Excel da web | ✗ Fallisce | ✓ Funziona |
| Complessità | - | Bassa (10 min) |
| Rischio | - | Nullo |

---

**Tempo totale stimato**: 10-15 minuti  
**Livello difficoltà**: ⭐⭐☆☆☆ (Facile)  
**Compatibilità**: Windows 10/11 ✓

Buona installazione! 🚀

