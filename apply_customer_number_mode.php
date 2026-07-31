<?php

$root = __DIR__;
$routePath = $root . '/routes/web.php';
$backupPath = $root . '/storage/app/customer_number_backup_' . date('Ymd_His') . '.php';

if (!is_file($routePath)) {
    fwrite(STDERR, "[GAGAL] routes/web.php tidak ditemukan.\n");
    exit(1);
}

$content = file_get_contents($routePath);

if (!str_contains($content, "name('customers.generate-number')")) {
    $needle = "Route::post('/customers/import', [CustomerController::class, 'import'])";

    $route = <<<'PHP'
    Route::get('/customers/generate-number', [CustomerController::class, 'generateNumber'])
        ->name('customers.generate-number');
    Route::post('/customers/import', [CustomerController::class, 'import'])
PHP;

    if (!str_contains($content, $needle)) {
        fwrite(STDERR, "[GAGAL] Route import pelanggan tidak ditemukan. Pasang Sprint 6.4 terlebih dahulu.\n");
        exit(1);
    }

    @mkdir(dirname($backupPath), 0777, true);
    copy($routePath, $backupPath);
    $content = str_replace($needle, $route, $content);
    file_put_contents($routePath, $content);

    echo "[OK] Route generate nomor pelanggan ditambahkan.\n";
    echo "[INFO] Backup route: {$backupPath}\n";
} else {
    echo "[INFO] Route generate nomor pelanggan sudah tersedia.\n";
}

echo "\nJalankan:\n";
echo "php artisan view:clear\n";
echo "php artisan optimize:clear\n";
echo "php artisan route:list --path=customers\n";
