<?php

$root = __DIR__;
$backupRoot = $root . '/storage/app/sprint_6_4_4_backup_' . date('Ymd_His');

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
    file_put_contents($path, $new);

    echo "[OK] " . str_replace($root, '', $path) . PHP_EOL;
}

/*
|--------------------------------------------------------------------------
| 1. Detail Registrasi: Marketing hanya melihat
|--------------------------------------------------------------------------
*/
patchFile(
    $root . '/resources/views/registrations/show.blade.php',
    function (string $content): string {
        if (!str_contains($content, "@role('Super User|Super Admin|Admin')")) {
            $old = <<<'BLADE'
            <a href="{{ route('registrations.status.edit', $registration) }}"
            class="rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-600">
                Ubah Status

</a>
            <a href="{{ route('registrations.edit', $registration) }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-indigo-700">
                ✏ Edit
            </a>
BLADE;

            $new = <<<'BLADE'
            @role('Super User|Super Admin|Admin')
                <a href="{{ route('registrations.status.edit', $registration) }}"
                   class="rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-600">
                    Ubah Status
                </a>

                <a href="{{ route('registrations.edit', $registration) }}"
                   class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-indigo-700">
                    ✏ Edit
                </a>
            @endrole
BLADE;

            if (!str_contains($content, $old)) {
                failPatch('Blok tombol Ubah Status dan Edit tidak ditemukan pada detail Registrasi.');
            }

            $content = str_replace($old, $new, $content);
        }

        /*
         * Seluruh kartu aksi dapat mengubah status atau membuat Work Order.
         * Marketing tetap dapat melihat informasi, peta, KTP, dan riwayat.
         */
        if (!str_contains($content, "@role('Super User|Super Admin|Admin')\n            {{-- Action Registrasi --}}")) {
            $content = str_replace(
                '            {{-- Action Registrasi --}}',
                "            @role('Super User|Super Admin|Admin')\n            {{-- Action Registrasi --}}",
                $content
            );

            $content = str_replace(
                "            </div>\n\n        </div>\n    </div>",
                "            </div>\n            @endrole\n\n        </div>\n    </div>",
                $content
            );
        }

        return $content;
    },
    $root,
    $backupRoot
);

/*
|--------------------------------------------------------------------------
| 2. Controller: cegah Marketing mengetik URL langsung
|--------------------------------------------------------------------------
*/
patchFile(
    $root . '/app/Http/Controllers/RegistrationController.php',
    function (string $content): string {
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

        if (!str_contains($content, 'private function ensureAdminOnly()')) {
            $lastBrace = strrpos($content, '}');

            if ($lastBrace === false) {
                failPatch('Penutup class RegistrationController tidak ditemukan.');
            }

            $content = substr($content, 0, $lastBrace)
                . $guard
                . substr($content, $lastBrace);
        }

        $methods = [
            'public function edit(Registration $registration)' => "public function edit(Registration \$registration)\n{\n    \$this->ensureAdminOnly();",
            'public function update(Request $request, Registration $registration)' => "public function update(Request \$request, Registration \$registration)\n{\n    \$this->ensureAdminOnly();",
            'public function destroy(Registration $registration)' => "public function destroy(Registration \$registration)\n{\n    \$this->ensureAdminOnly();",
            'public function editStatus(Registration $registration)' => "public function editStatus(Registration \$registration)\n{\n    \$this->ensureAdminOnly();",
            'public function updateStatus(Request $request, Registration $registration)' => "public function updateStatus(Request \$request, Registration \$registration)\n{\n    \$this->ensureAdminOnly();",
            'public function verify(Registration $registration)' => "public function verify(Registration \$registration)\n{\n    \$this->ensureAdminOnly();",
        ];

        foreach ($methods as $signature => $replacement) {
            $pattern = $signature . "\n{";

            if (str_contains($content, $replacement)) {
                continue;
            }

            if (!str_contains($content, $pattern)) {
                failPatch("Method tidak ditemukan: {$signature}");
            }

            $content = str_replace($pattern, $replacement, $content);
        }

        return $content;
    },
    $root,
    $backupRoot
);

echo PHP_EOL;
echo "Sprint 6.4.4 berhasil diterapkan." . PHP_EOL;
echo "Backup: {$backupRoot}" . PHP_EOL;
echo PHP_EOL;
echo "Jalankan:" . PHP_EOL;
echo "php artisan view:clear" . PHP_EOL;
echo "php artisan route:clear" . PHP_EOL;
echo "php artisan optimize:clear" . PHP_EOL;
echo "php artisan view:cache" . PHP_EOL;
