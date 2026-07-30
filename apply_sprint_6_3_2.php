<?php

$root = __DIR__;
$backup = $root . '/storage/app/sprint_6_3_2_backup_' . date('Ymd_His');

function patchFile(string $path, callable $callback, string $root, string $backup): void
{
    if (!is_file($path)) {
        throw new RuntimeException("File tidak ditemukan: {$path}");
    }

    $content = file_get_contents($path);
    $updated = $callback($content);

    if ($updated === $content) {
        echo "[INFO] Tidak ada perubahan: " . str_replace($root, '', $path) . PHP_EOL;
        return;
    }

    $target = $backup . str_replace($root, '', $path);
    @mkdir(dirname($target), 0777, true);
    copy($path, $target);
    file_put_contents($path, $updated);

    echo "[OK] " . str_replace($root, '', $path) . PHP_EOL;
}

/* ROUTES */
patchFile($root . '/routes/web.php', function (string $content): string {
    if (!str_contains($content, "name('noc-activations.processing')")) {
        $needle = "Route::get('/', [NocActivationController::class, 'index'])\n            ->name('noc-activations.index');";
        $replacement = $needle . "\n        Route::get('/process', [NocActivationController::class, 'processingIndex'])\n            ->middleware('permission:noc-activations.process')\n            ->name('noc-activations.processing');";
        $content = str_replace($needle, $replacement, $content);
    }

    if (!str_contains($content, "name('noc-activations.verify-admin')")) {
        $needle = "Route::post('/{nocActivation}/complete', [NocActivationController::class, 'complete'])\n            ->middleware('permission:noc-activations.process')\n            ->name('noc-activations.complete');";
        $replacement = $needle . "\n        Route::post('/{nocActivation}/verify-admin', [NocActivationController::class, 'verifyAdmin'])\n            ->middleware('permission:noc-activations.verify')\n            ->name('noc-activations.verify-admin');";
        $content = str_replace($needle, $replacement, $content);
    }

    return $content;
}, $root, $backup);

/* SIDEBAR */
patchFile($root . '/resources/views/layouts/sidebar.blade.php', function (string $content): string {
    $content = str_replace(
        "route('noc-activations.index', ['status' => \\App\\Models\\NocActivation::STATUS_PROCESSING])",
        "route('noc-activations.processing')",
        $content
    );

    $content = str_replace(
        "request('status') === \\App\\Models\\NocActivation::STATUS_PROCESSING || request()->routeIs('noc-activations.process')",
        "request()->routeIs('noc-activations.processing') || request()->routeIs('noc-activations.process')",
        $content
    );

    $content = str_replace(
        "request()->routeIs('noc-activations.*') ? 'text-white font-semibold'",
        "request()->routeIs('noc-activations.index') ? 'text-white font-semibold'",
        $content
    );

    $content = str_replace(
        "request()->routeIs('noc-activations.*') ? 'bg-white'",
        "request()->routeIs('noc-activations.index') ? 'bg-white'",
        $content
    );

    return $content;
}, $root, $backup);

/* ROUTER CREATE/EDIT: sisipkan partial sebelum Status */
foreach (['create.blade.php', 'edit.blade.php'] as $file) {
    patchFile($root . '/resources/views/routers/' . $file, function (string $content): string {
        if (str_contains($content, "routers._olt_generator_fields")) {
            return $content;
        }

        $marker = '{{-- Status --}}';
        return str_replace(
            $marker,
            "@include('routers._olt_generator_fields')\n\n            " . $marker,
            $content
        );
    }, $root, $backup);
}

echo PHP_EOL . "Sprint 6.3.2 terpasang." . PHP_EOL;
echo "Backup: {$backup}" . PHP_EOL;
echo "Lanjutkan dengan:" . PHP_EOL;
echo "php artisan migrate" . PHP_EOL;
echo "php artisan permission:cache-reset" . PHP_EOL;
echo "php artisan optimize:clear" . PHP_EOL;
echo "php artisan route:list --path=activation" . PHP_EOL;
