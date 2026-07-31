<?php

$root = __DIR__;
$backupRoot = $root . '/storage/app/sprint_6_4_4_v2_backup_' . date('Ymd_His');

function failPatch(string $message): never
{
    fwrite(STDERR, "[GAGAL] {$message}\n");
    exit(1);
}

function patchFile(string $path, callable $callback, string $root, string $backupRoot): void
{
    if (!is_file($path)) {
        failPatch("File tidak ditemukan: {$path}");
    }

    $old = file_get_contents($path);
    $new = $callback($old);

    if ($new === $old) {
        echo "[INFO] Tidak ada perubahan: " . str_replace($root, '', $path) . PHP_EOL;
        return;
    }

    $backup = $backupRoot . str_replace($root, '', $path);
    @mkdir(dirname($backup), 0777, true);
    copy($path, $backup);

    if (file_put_contents($path, $new) === false) {
        failPatch("Tidak dapat menulis file: {$path}");
    }

    echo "[OK] " . str_replace($root, '', $path) . PHP_EOL;
}

/*
|--------------------------------------------------------------------------
| 1. Detail Registrasi
|--------------------------------------------------------------------------
*/
patchFile(
    $root . '/resources/views/registrations/show.blade.php',
    function (string $content): string {
        /*
         * Tombol header:
         * Kembali selalu terlihat.
         * Ubah Status dan Edit hanya untuk role Admin.
         */
        if (!str_contains($content, 'registrations-admin-header-actions')) {
            $pattern = <<<'REGEX'
~(?P<status><a\s+href="\{\{\s*route\('registrations\.status\.edit',\s*\$registration\)\s*\}\}"[\s\S]*?</a>)\s*(?P<edit><a\s+href="\{\{\s*route\('registrations\.edit',\s*\$registration\)\s*\}\}"[\s\S]*?</a>)~
REGEX;

            $replacement = <<<'BLADE'
{{-- registrations-admin-header-actions --}}
@role('Super User|Super Admin|Admin')
    ${status}
    ${edit}
@endrole
BLADE;

            $updated = preg_replace($pattern, $replacement, $content, 1, $count);

            if ($updated === null || $count !== 1) {
                failPatch('Tombol Ubah Status dan Edit tidak berhasil ditemukan secara adaptif.');
            }

            $content = $updated;
        }

        /*
         * Kartu aksi kanan:
         * Verifikasi, Jadwalkan Teknisi, dan aksi perubahan proses hanya Admin.
         */
        if (!str_contains($content, 'registrations-admin-action-card')) {
            $pattern = <<<'REGEX'
~(?P<card>\{\{--\s*Action Registrasi\s*--\}\}\s*<div\s+class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">[\s\S]*?</div>)\s*(?=\s*</div>\s*</div>\s*</div>\s*@endsection)~
REGEX;

            $replacement = <<<'BLADE'
{{-- registrations-admin-action-card --}}
@role('Super User|Super Admin|Admin')
${card}
@endrole
BLADE;

            $updated = preg_replace($pattern, $replacement, $content, 1, $count);

            if ($updated === null || $count !== 1) {
                failPatch('Kartu aksi Registrasi tidak berhasil ditemukan.');
            }

            $content = $updated;
        }

        return $content;
    },
    $root,
    $backupRoot
);

/*
|--------------------------------------------------------------------------
| 2. RegistrationController
|--------------------------------------------------------------------------
*/
patchFile(
    $root . '/app/Http/Controllers/RegistrationController.php',
    function (string $content): string {
        if (!str_contains($content, 'private function ensureAdminOnly(): void')) {
            $guard = <<<'PHP'

    private function ensureAdminOnly(): void
    {
        abort_unless(
            auth()->check()
                && auth()->user()->hasAnyRole([
                    'Super User',
                    'Super Admin',
                    'Admin',
                ]),
            403,
            'Hanya Admin yang dapat mengubah data atau status Registrasi.'
        );
    }

PHP;

            $lastBrace = strrpos($content, '}');

            if ($lastBrace === false) {
                failPatch('Penutup RegistrationController tidak ditemukan.');
            }

            $content = substr($content, 0, $lastBrace)
                . $guard
                . substr($content, $lastBrace);
        }

        $methods = [
            'edit',
            'update',
            'destroy',
            'editStatus',
            'updateStatus',
            'verify',
        ];

        foreach ($methods as $method) {
            if (preg_match(
                '~public\s+function\s+' . preg_quote($method, '~') . '\s*\([^)]*\)\s*\{\s*\$this->ensureAdminOnly\(\);~s',
                $content
            )) {
                continue;
            }

            $pattern = '~(public\s+function\s+' . preg_quote($method, '~') . '\s*\([^)]*\)\s*\{)~s';
            $replacement = "$1\n        \$this->ensureAdminOnly();";

            $updated = preg_replace($pattern, $replacement, $content, 1, $count);

            if ($updated === null || $count !== 1) {
                failPatch("Method {$method} tidak ditemukan pada RegistrationController.");
            }

            $content = $updated;
        }

        return $content;
    },
    $root,
    $backupRoot
);

echo PHP_EOL;
echo "Sprint 6.4.4 v2 berhasil diterapkan." . PHP_EOL;
echo "Backup: {$backupRoot}" . PHP_EOL;
echo PHP_EOL;
echo "Jalankan:" . PHP_EOL;
echo "php artisan view:clear" . PHP_EOL;
echo "php artisan route:clear" . PHP_EOL;
echo "php artisan optimize:clear" . PHP_EOL;
echo "php artisan view:cache" . PHP_EOL;
