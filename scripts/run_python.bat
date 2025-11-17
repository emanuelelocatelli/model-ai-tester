@echo off
REM Script wrapper per eseguire Python da Apache/MAMP
REM Cerca Python in ordine: MAMP, sistema, percorsi comuni

REM Imposta encoding UTF-8 per caratteri accentati
chcp 65001 >nul 2>&1
set PYTHONIOENCODING=utf-8

REM 1. PRIORITÀ: Python installato per MAMP/Apache (accessibile da SYSTEM)
if exist "C:\Python\314\python.exe" (
    "C:\Python\314\python.exe" %*
    exit /b %ERRORLEVEL%
)

if exist "C:\Python314\python.exe" (
    "C:\Python314\python.exe" %*
    exit /b %ERRORLEVEL%
)

if exist "C:\Python313\python.exe" (
    "C:\Python313\python.exe" %*
    exit /b %ERRORLEVEL%
)

if exist "C:\Python312\python.exe" (
    "C:\Python312\python.exe" %*
    exit /b %ERRORLEVEL%
)

if exist "C:\Python311\python.exe" (
    "C:\Python311\python.exe" %*
    exit /b %ERRORLEVEL%
)

if exist "C:\Program Files\Python312\python.exe" (
    "C:\Program Files\Python312\python.exe" %*
    exit /b %ERRORLEVEL%
)

if exist "C:\Program Files\Python311\python.exe" (
    "C:\Program Files\Python311\python.exe" %*
    exit /b %ERRORLEVEL%
)

REM 2. Prova Python di MAMP se esiste
if exist "C:\MAMP\bin\python\python.exe" (
    "C:\MAMP\bin\python\python.exe" %*
    exit /b %ERRORLEVEL%
)

REM 3. Fallback: Python Microsoft Store (potrebbe non funzionare da Apache)
if exist "C:\Users\emanu\AppData\Local\Microsoft\WindowsApps\PythonSoftwareFoundation.Python.3.11_qbz5n2kfra8p0\python.exe" (
    "C:\Users\emanu\AppData\Local\Microsoft\WindowsApps\PythonSoftwareFoundation.Python.3.11_qbz5n2kfra8p0\python.exe" %*
    exit /b %ERRORLEVEL%
)

REM 2. Prova Python nel PATH di sistema
where python >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    python %*
    exit /b %ERRORLEVEL%
)

REM 3. Prova percorsi comuni di installazione
if exist "C:\Python39\python.exe" (
    "C:\Python39\python.exe" %*
    exit /b %ERRORLEVEL%
)

if exist "C:\Python310\python.exe" (
    "C:\Python310\python.exe" %*
    exit /b %ERRORLEVEL%
)

if exist "C:\Python311\python.exe" (
    "C:\Python311\python.exe" %*
    exit /b %ERRORLEVEL%
)

if exist "C:\Program Files\Python39\python.exe" (
    "C:\Program Files\Python39\python.exe" %*
    exit /b %ERRORLEVEL%
)

if exist "C:\Program Files\Python310\python.exe" (
    "C:\Program Files\Python310\python.exe" %*
    exit /b %ERRORLEVEL%
)

if exist "C:\Program Files\Python311\python.exe" (
    "C:\Program Files\Python311\python.exe" %*
    exit /b %ERRORLEVEL%
)

REM 4. Python non trovato - errore
echo ============================================
echo ERRORE: Python non trovato nel sistema
echo ============================================
echo.
echo Soluzioni possibili:
echo 1. Installare Python da python.org
echo 2. Aggiungere Python al PATH di sistema
echo 3. Installare MAMP con Python incluso
echo.
echo Percorsi cercati:
echo - C:\MAMP\bin\python\python.exe
echo - PATH di sistema
echo - C:\Python39, C:\Python310, C:\Python311
echo - C:\Program Files\Python39, Python310, Python311
echo.
exit /b 1
