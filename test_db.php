<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $result = DB::connection()->getPdo();
    echo "Database connection successful!\n";
    
    $tables = DB::select('SHOW TABLES');
    echo "\nTables in database:\n";
    foreach ($tables as $table) {
        foreach ($table as $name) {
            echo "- $name\n";
        }
    }
    
} catch (\Exception $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
