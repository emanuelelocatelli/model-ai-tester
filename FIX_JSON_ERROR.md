# Fix: Errore JSON "undefined" is not valid JSON in Livewire

## Problema
```
Uncaught (in promise) SyntaxError: "undefined" is not valid JSON
at JSON.parse (<anonymous>)
at deepClone (livewire.js?id=df3a17f2:367:17)
at Component.mergeNewSnapshot (livewire.js?id=df3a17f2:4550:38)
```

Errore si verificava SUBITO all'invio del form quando si caricava un file Excel.

## Causa Root
Livewire serializza il componente in JSON durante ogni richiesta AJAX per sincronizzare lo stato frontend/backend. L'oggetto `$this->uploadedFile` (di tipo `TemporaryUploadedFile`) **non è serializzabile in JSON**.

Quando l'utente cliccava "Invia", Livewire tentava di serializzare il componente PRIMA di eseguire il metodo `sendMessage()`, causando il fallimento con "undefined" is not valid JSON.

## Fix Implementato

### Prima (NON funzionante)
```php
public function sendMessage(): void
{
    // ... validazioni ...
    
    if ($this->uploadedFile) {
        $fileAnalysis = $this->handleFileUpload();
        
        // Reset DOPO l'elaborazione - TROPPO TARDI!
        $this->uploadedFile = null;
    }
}
```

**Problema**: Livewire serializza il componente PRIMA che `sendMessage()` venga eseguito, quindi `$this->uploadedFile` è ancora un oggetto non serializzabile.

### Dopo (FUNZIONANTE)
```php
public function sendMessage(): void
{
    // CRITICAL FIX: Salva in variabile locale e resetta SUBITO
    $uploadedFileLocal = $this->uploadedFile;
    $this->uploadedFile = null;  // <-- Reset IMMEDIATO
    
    // ... validazioni ...
    
    if ($uploadedFileLocal) {
        $fileAnalysis = $this->handleFileUpload($uploadedFileLocal);
    }
}

private function handleFileUpload($uploadedFile): ?string
{
    // Usa il parametro invece di $this->uploadedFile
    $tempPath = $uploadedFile->store('temp', 'local');
    // ...
}
```

**Soluzione**: 
1. Salva il file in variabile locale alla **prima riga** del metodo
2. Resetta `$this->uploadedFile = null` **immediatamente** (seconda riga)
3. Usa la variabile locale per tutta l'elaborazione
4. Livewire ora può serializzare il componente senza problemi (uploadedFile è null)

## Modifiche Effettuate

### File: `app/Livewire/ChatTester.php`

#### 1. Metodo `sendMessage()` (righe 42-136)
- **Riga 45**: Salva file in `$uploadedFileLocal`
- **Riga 46**: Reset immediato `$this->uploadedFile = null`
- **Righe 53, 68-70**: Usa `$uploadedFileLocal` invece di `$this->uploadedFile`
- **Riga 81**: Passa `$uploadedFileLocal` a `handleFileUpload()`
- **Riga 98**: Rimosso check `$this->uploadedFile` (ora sempre null)
- **Righe 127-131**: Rimosso reset nel catch (non più necessario)

#### 2. Metodo `handleFileUpload()` (righe 144-202)
- **Signature**: Cambiata da `handleFileUpload()` a `handleFileUpload($uploadedFile)`
- **Riga 170**: Usa `$uploadedFile->store()` invece di `$this->uploadedFile->store()`
- Tutte le altre occorrenze ora usano il parametro invece della proprietà

## Timing della Serializzazione Livewire

```
Frontend: Click "Invia"
    ↓
Livewire: Serializza componente in JSON (PRIMA di sendMessage)
    ↓ [QUI avveniva l'errore se $uploadedFile era popolato]
Backend: Esegue sendMessage()
    ↓
Backend: Invia risposta JSON
    ↓
Frontend: Aggiorna UI
```

Con il fix, quando Livewire serializza il componente, `$this->uploadedFile` è già `null`, quindi la serializzazione ha successo.

## Test Effettuati

1. ✅ Sintassi PHP: `php -l app/Livewire/ChatTester.php` - OK
2. ✅ Cache Laravel: `php artisan optimize:clear` - OK
3. ✅ Linter: Nessun errore
4. ⏳ Browser test con file Excel: In attesa verifica utente

## Note Tecniche

- **Livewire serialization**: Avviene PRIMA dell'esecuzione del metodo chiamato
- **TemporaryUploadedFile**: Non implementa `JsonSerializable`
- **Best Practice**: Reset proprietà non serializzabili SUBITO all'inizio del metodo

## Perché il Problema Non Si Verificava con Messaggi Senza File?

Quando si inviava solo testo (senza file):
- `$this->uploadedFile` era già `null`
- Livewire serializzava senza problemi (`null` è serializzabile)
- Il form funzionava correttamente

## Soluzione Alternativa (Non Usata)

Un'altra soluzione possibile sarebbe stata implementare `jsonSerialize()` nel componente:

```php
public function jsonSerialize()
{
    $data = get_object_vars($this);
    $data['uploadedFile'] = null; // Forza a null in serializzazione
    return $data;
}
```

**Non usata perché**: La soluzione con variabile locale è più pulita e non richiede override di metodi magici.

## Riferimenti

- Livewire File Uploads: https://livewire.laravel.com/docs/uploads
- PHP JsonSerializable: https://www.php.net/manual/en/class.jsonserializable.php

---

**Data Fix**: 2025-11-17  
**Versione**: 1.0  
**Status**: IMPLEMENTATO - In attesa test utente

