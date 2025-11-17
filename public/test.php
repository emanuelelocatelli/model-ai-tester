<?php
/**
 * Test Rapido - Verifica che tutto funzioni
 */

echo "<h1>✅ Test Configurazione</h1>";

echo "<h2>1. PHP & Estensioni</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "mbstring: " . (extension_loaded('mbstring') ? '✓' : '✗') . "<br>";
echo "openssl: " . (extension_loaded('openssl') ? '✓' : '✗') . "<br>";

echo "<h2>2. Laravel</h2>";
if (file_exists('../vendor/autoload.php')) {
    require '../vendor/autoload.php';
    $app = require_once '../bootstrap/app.php';
    echo "Laravel: ✓ Caricato<br>";
    echo "Livewire: " . (class_exists('Livewire\Component') ? '✓' : '✗') . "<br>";
} else {
    echo "Laravel: ✗ Non trovato<br>";
}

echo "<h2>3. Sessioni</h2>";
session_start();
$_SESSION['test'] = 'OK';
echo "Sessioni: " . ($_SESSION['test'] === 'OK' ? '✓ Funzionanti' : '✗') . "<br>";

echo "<h2>4. Python</h2>";
$python = shell_exec('python --version 2>&1');
echo "Python: " . ($python ? '✓ ' . trim($python) : '✗ Non trovato') . "<br>";

echo "<hr>";
echo "<h3>🎉 Se vedi tutti ✓, l'applicazione è pronta!</h3>";
echo "<p><a href='/'>→ Vai all'applicazione</a></p>";


