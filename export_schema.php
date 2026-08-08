<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$tables = Schema::getTables();
$db = [];

foreach($tables as $t) {
    $tableName = $t['name'];
    if (in_array($tableName, ['migrations', 'failed_jobs', 'password_reset_tokens', 'personal_access_tokens', 'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches'])) continue;
    
    $columns = Schema::getColumns($tableName);
    $fks = Schema::getForeignKeys($tableName);
    
    $db[$tableName] = [
        'columns' => array_map(function($c) {
            return [
                'name' => $c['name'],
                'type' => $c['type_name'],
            ];
        }, $columns),
        'foreign_keys' => array_map(function($fk) {
            return [
                'local' => $fk['columns'][0],
                'foreign_table' => $fk['foreign_table'],
                'foreign_col' => $fk['foreign_columns'][0]
            ];
        }, $fks)
    ];
}

file_put_contents('schema.json', json_encode($db, JSON_PRETTY_PRINT));
