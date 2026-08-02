<?php

/**
 * Emergency repair for RegistrationController ParseError.
 *
 * Jalankan dari root FN-WMS:
 * php repair_registration_controller_v3.php
 */

$root = __DIR__;
$path = $root . '/app/Http/Controllers/RegistrationController.php';
$backup = $root . '/storage/app/registration_controller_backup_' . date('Ymd_His') . '.php';

if (!is_file($path)) {
    fwrite(STDERR, "[GAGAL] RegistrationController.php tidak ditemukan.\n");
    exit(1);
}

$content = file_get_contents($path);

if ($content === false) {
    fwrite(STDERR, "[GAGAL] RegistrationController.php tidak dapat dibaca.\n");
    exit(1);
}

@mkdir(dirname($backup), 0777, true);

if (!copy($path, $backup)) {
    fwrite(STDERR, "[GAGAL] Backup controller tidak dapat dibuat.\n");
    exit(1);
}

/*
|--------------------------------------------------------------------------
| Hapus sisa helper lama yang tertinggal setelah helper baru
|--------------------------------------------------------------------------
|
| Bentuk error dari screenshot:
|
|     }
|     return \App\Models\User::role('Marketing')
|         ->orderBy('name')
|         ->get();
|     }
| }
|
| Blok return User::role() tersebut berada di luar function.
|
*/
$orphanPattern = <<<'REGEX'
~\n\s*return\s+\\App\\Models\\User::role\(['"]Marketing['"]\)\s*
\s*->orderBy\(['"]name['"]\)\s*
\s*->get\(\);\s*
\s*\}\s*
(?=\s*\})~x
REGEX;

$repaired = preg_replace(
    $orphanPattern,
    "\n",
    $content,
    1,
    $removedCount
);

if ($repaired === null) {
    fwrite(STDERR, "[GAGAL] Pola perbaikan tidak valid.\n");
    exit(1);
}

if ($removedCount === 0) {
    /*
     * Alternatif lebih longgar untuk perbedaan spasi/baris.
     */
    $orphanPatternAlternative = <<<'REGEX'
~\s*return\s+\\App\\Models\\User::role\(['"]Marketing['"]\)[\s\S]*?->get\(\);\s*\}\s*(?=\})~
REGEX;

    $repaired = preg_replace(
        $orphanPatternAlternative,
        "\n",
        $content,
        1,
        $removedCount
    );
}

if ($repaired === null || $removedCount !== 1) {
    fwrite(STDERR, "[GAGAL] Blok helper lama yang rusak tidak ditemukan tepat satu kali.\n");
    fwrite(STDERR, "Backup tetap tersedia di: {$backup}\n");
    exit(1);
}

/*
|--------------------------------------------------------------------------
| Pastikan helper baru memiliki struktur yang benar
|--------------------------------------------------------------------------
*/
$expectedHelper = <<<'PHP'
    private function marketingUsersForForm(Request $request)
    {
        if ($request->user()->hasRole('Marketing')) {
            return \App\Models\Marketing::query()
                ->with('user')
                ->where('user_id', $request->user()->id)
                ->get();
        }

        return \App\Models\Marketing::query()
            ->with('user')
            ->get()
            ->sortBy(fn ($marketing) => $marketing->user->name ?? '');
    }
PHP;

$helperPattern = <<<'REGEX'
~\s*private\s+function\s+marketingUsersForForm\s*\(Request\s+\$request\)\s*\{[\s\S]*?\n\s*\}\s*(?=\n\s*\})~
REGEX;

if (preg_match($helperPattern, $repaired)) {
    $repaired = preg_replace(
        $helperPattern,
        "\n" . $expectedHelper . "\n",
        $repaired,
        1
    );
}

if (file_put_contents($path, $repaired) === false) {
    fwrite(STDERR, "[GAGAL] Controller tidak dapat ditulis.\n");
    exit(1);
}

echo "[OK] Sisa helper lama berhasil dihapus.\n";
echo "[OK] RegistrationController diperbaiki.\n";
echo "[INFO] Backup: {$backup}\n\n";

echo "Jalankan pemeriksaan berikut:\n";
echo "php -l app/Http/Controllers/RegistrationController.php\n";
echo "php artisan optimize:clear\n";
echo "php artisan route:clear\n";
echo "php artisan view:clear\n";
