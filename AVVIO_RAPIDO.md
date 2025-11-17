# 🚀 Avvio Rapido - AI Model Tester

## ✅ Stato Configurazione

✓ Laravel 12 installato
✓ Livewire 3 configurato
✓ Python/Pandas installato
✓ Tutte le dipendenze pronte
✓ MAMP PRO configurato con virtual host
✓ APP_URL: `http://model-ai-tester.local`

---

## 📝 Prossimi Passi (IMPORTANTI)

### 1️⃣ Configura OpenAI API Key

Apri il file `.env` e sostituisci:

```env
OPENAI_API_KEY=your-api-key-here
```

Con la tua chiave reale:

```env
OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxxxxxxxxxx
```

### 2️⃣ Avvia MAMP PRO

1. Apri **MAMP PRO**
2. Verifica che il virtual host **`model-ai-tester.local`** sia presente
3. **Start** i servizi Apache/Nginx

### 3️⃣ Accedi all'Applicazione

Apri il browser e vai su:

```
http://model-ai-tester.local
```

---

## 🎯 Come Usare

### Test Messaggio Semplice
1. Scrivi: "Ciao, chi sei?"
2. Premi **Invio** o **Ctrl+Enter**

### Test Analisi Excel
1. Clicca sull'icona 📎 (upload file)
2. Seleziona `test_sales_data.xlsx`
3. Scrivi: "Analizza questi dati di vendita"
4. Invia

### Test PDF
1. Carica un file PDF
2. Scrivi: "Riassumi questo documento"
3. Invia

---

## ⚠️ Risoluzione Problemi

### Errore: "Database file does not exist"
**Risolto!** Le sessioni ora usano file system, non database.

### Errore 500 Generico
Esegui questi comandi in PowerShell:

```powershell
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

Poi **riavvia i servizi in MAMP PRO**.

---

## 📱 Caratteristiche

✨ **Modelli AI:** GPT-4o, **GPT-4.1**, **GPT-5.1**, GPT-4o Mini, O1 Preview, O1 Mini
🎯 **Modello Predefinito:** GPT-4.1
📊 **Analisi Excel:** Python/Pandas automatico
📄 **Estrazione PDF:** Testo completo
💬 **Chat:** Cronologia conversazione
🎨 **UI Moderna:** Tailwind CSS, animazioni, responsive

---

## 🆘 Supporto

**Log Laravel:** `storage/logs/laravel.log`
**Log MAMP:** `C:\MAMP\logs\php_error.log`

**Documentazione completa:** Vedi `README_SETUP.md`

---

**Buon testing! 🎉**

