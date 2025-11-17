<?php
header('Content-Type: text/plain; charset=utf-8');

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Log;

echo "=== FORCE TEST - Esecuzione dal WEB SERVER ===\n\n";

// Pulisci log
if (file_exists(storage_path('logs/laravel.log'))) {
    file_put_contents(storage_path('logs/laravel.log'), '');
}

$scriptPath = __DIR__ . '\\..\\scripts\\analyze_excel.py';
$filePath = __DIR__ . '\\..\\test_sales_data.xlsx';
$scriptPath = str_replace('/', '\\', realpath($scriptPath));
$filePath = str_replace('/', '\\', realpath($filePath));

echo "Script: $scriptPath\n";
echo "File: $filePath\n";
echo "Script esiste: " . (file_exists($scriptPath) ? 'SI' : 'NO') . "\n";
echo "File esiste: " . (file_exists($filePath) ? 'SI' : 'NO') . "\n\n";

$batPath = __DIR__ . '\\..\\scripts\\run_python.bat';
$batPath = str_replace('/', '\\', realpath($batPath));

$pythonCommands = [
    $batPath,  // Batch file wrapper
    'python',
    'py',
];

foreach ($pythonCommands as $pyCmd) {
    $command = sprintf('%s "%s" "%s" 2>&1', $pyCmd, $scriptPath, $filePath);
    
    echo "====================================\n";
    echo "Testing: $pyCmd\n";
    echo "Command: $command\n";
    echo "Python esiste: " . (file_exists($pyCmd) ? 'SI' : 'NO/NA') . "\n";
    
    Log::info('FORCE TEST', [
        'pyCmd' => $pyCmd,
        'command' => $command,
        'pythonExists' => file_exists($pyCmd)
    ]);
    
    $result = shell_exec($command);
    
    if ($result && strpos($result, 'ANALISI COMPLETATA') !== false) {
        echo "✓✓✓ SUCCESS ✓✓✓\n";
        echo "Length: " . strlen($result) . " bytes\n";
        echo "First 200 chars:\n" . substr($result, 0, 200) . "...\n";
        
        Log::info('FORCE TEST SUCCESS', [
            'pyCmd' => $pyCmd,
            'length' => strlen($result)
        ]);
        
        echo "\n=== QUESTO COMANDO FUNZIONA DAL WEB SERVER! ===\n";
        echo "Ora ricarica l'applicazione e riprova.\n";
        break;
    } else {
        echo "FAILED\n";
        echo "Result: " . substr($result ?? 'NULL', 0, 200) . "\n";
        
        Log::info('FORCE TEST FAILED', [
            'pyCmd' => $pyCmd,
            'result' => substr($result ?? 'NULL', 0, 200)
        ]);
    }
}

echo "\n\nControlla anche storage/logs/laravel.log per dettagli.\n";

