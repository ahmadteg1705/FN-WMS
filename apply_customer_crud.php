<?php

$root = __DIR__;
$routePath = $root . '/routes/web.php';
$backupPath = $root . '/storage/app/customer_crud_backup_' . date('Ymd_His') . '.php';

if (!is_file($routePath)) {
    fwrite(STDERR, "[GAGAL] routes/web.php tidak ditemukan.\n");
    exit(1);
}

$content = file_get_contents($routePath);

if (!str_contains($content, "name('customers.import')")) {
    $needle = "Route::resource('customers', CustomerController::class);";

    $routes = <<<'PHP'
    Route::post('/customers/import', [CustomerController::class, 'import'])
        ->name('customers.import');
    Route::get('/customers/export', [CustomerController::class, 'export'])
        ->name('customers.export');
    Route::get('/customers/template', [CustomerController::class, 'downloadTemplate'])
        ->name('customers.template');
    Route::resource('customers', CustomerController::class);
PHP;

    if (!str_contains($content, $needle)) {
        fwrite(STDERR, "[GAGAL] Route resource customers tidak ditemukan.\n");
        exit(1);
    }

    @mkdir(dirname($backupPath), 0777, true);
    copy($routePath, $backupPath);
    $content = str_replace($needle, $routes, $content);
    file_put_contents($routePath, $content);

    echo "[OK] Route Import, Export, Template, Detail, Edit dan Hapus pelanggan diterapkan.\n";
    echo "[INFO] Backup route: {$backupPath}\n";
} else {
    echo "[INFO] Route CRUD pelanggan sudah tersedia.\n";
}

echo "\nJalankan:\n";
echo "php artisan storage:link\n";
echo "php artisan view:clear\n";
echo "php artisan optimize:clear\n";
echo "php artisan route:list --path=customers\n";
