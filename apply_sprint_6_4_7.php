<?php

$root = __DIR__;
$path = $root . '/resources/views/registrations/show.blade.php';
$controllerPath = $root . '/app/Http/Controllers/RegistrationController.php';
$backupRoot = $root . '/storage/app/sprint_6_4_7_backup_' . date('Ymd_His');

function failPatch(string $message): never
{
    fwrite(STDERR, "[GAGAL] {$message}" . PHP_EOL);
    exit(1);
}

function backupAndWrite(string $path, string $content, string $root, string $backupRoot): void
{
    if (!is_file($path)) {
        failPatch("File tidak ditemukan: {$path}");
    }

    $backup = $backupRoot . str_replace($root, '', $path);
    @mkdir(dirname($backup), 0777, true);

    if (!copy($path, $backup)) {
        failPatch("Gagal membuat backup: {$path}");
    }

    if (file_put_contents($path, $content) === false) {
        failPatch("Gagal menulis file: {$path}");
    }

    echo "[OK] " . str_replace($root, '', $path) . PHP_EOL;
}

if (!is_file($path)) {
    failPatch('resources/views/registrations/show.blade.php tidak ditemukan.');
}

$content = file_get_contents($path);

if ($content === false) {
    failPatch('Detail Registrasi tidak dapat dibaca.');
}

/*
|--------------------------------------------------------------------------
| 1. Role Administrator harus dianggap Admin
|--------------------------------------------------------------------------
*/
$content = str_replace(
    "@role('Super User|Super Admin|Admin')",
    "@role('Super User|Super Admin|Admin|Administrator')",
    $content
);

/*
|--------------------------------------------------------------------------
| 2. Pulihkan kartu aksi yang berubah menjadi literal ${card}
|--------------------------------------------------------------------------
*/
$actionCard = <<<'BLADE'
{{-- Action Registrasi: hanya Admin --}}
@role('Super User|Super Admin|Admin|Administrator')
<div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

    @if($registration->status == 'Registrasi Baru')
        <form
            action="{{ route('registrations.verify', $registration) }}"
            method="POST"
        >
            @csrf
            <button
                type="submit"
                onclick="return confirm('Verifikasi registrasi ini?')"
                class="w-full rounded-lg bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-700"
            >
                ✓ Verifikasi
            </button>
        </form>

    @elseif($registration->status == 'Diverifikasi')
        @if(!$registration->workOrder)
            <a
                href="{{ route('work-orders.create', ['registration' => $registration->id]) }}"
                class="inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow hover:bg-blue-700"
            >
                📅 Jadwalkan Teknisi
            </a>
        @else
            <a
                href="{{ route('work-orders.show', $registration->workOrder) }}"
                class="inline-flex w-full items-center justify-center rounded-lg bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow hover:bg-emerald-700"
            >
                👷 Lihat Work Order
            </a>
        @endif
    @endif

</div>
@endrole
BLADE;

if (str_contains($content, '${card}')) {
    $content = str_replace('${card}', $actionCard, $content);
} elseif (!str_contains($content, "route('registrations.verify', \$registration)")) {
    /*
     * Jika literal sudah hilang tetapi kartu aksi juga hilang,
     * sisipkan sebelum penutup kolom kanan.
     */
    $needle = <<<'BLADE'
        </div>
    </div>

</div>
@endsection
BLADE;

    if (!str_contains($content, $needle)) {
        failPatch('Posisi penyisipan kartu Verifikasi tidak ditemukan.');
    }

    $replacement = <<<'BLADE'
            ACTION_CARD
        </div>
    </div>

</div>
@endsection
BLADE;

    $replacement = str_replace('ACTION_CARD', $actionCard, $replacement);
    $content = str_replace($needle, $replacement, $content);
}

/*
|--------------------------------------------------------------------------
| 3. Pastikan tombol header Admin juga mencakup Administrator
|--------------------------------------------------------------------------
*/
if (
    str_contains($content, "route('registrations.status.edit', \$registration)")
    && !str_contains($content, 'registrations-admin-header-actions')
) {
    $pattern = <<<'REGEX'
~(?P<status><a\s+href="\{\{\s*route\('registrations\.status\.edit',\s*\$registration\)\s*\}\}"[\s\S]*?</a>)\s*(?P<edit><a\s+href="\{\{\s*route\('registrations\.edit',\s*\$registration\)\s*\}\}"[\s\S]*?</a>)~
REGEX;

    $replacement = <<<'BLADE'
{{-- registrations-admin-header-actions --}}
@role('Super User|Super Admin|Admin|Administrator')
${status}
${edit}
@endrole
BLADE;

    $updated = preg_replace($pattern, $replacement, $content, 1, $count);

    if ($updated !== null && $count === 1) {
        $content = $updated;
    }
}

backupAndWrite($path, $content, $root, $backupRoot);

/*
|--------------------------------------------------------------------------
| 4. Controller: role Administrator juga boleh verifikasi
|--------------------------------------------------------------------------
*/
if (is_file($controllerPath)) {
    $controller = file_get_contents($controllerPath);

    if ($controller !== false) {
        $controller = str_replace(
            "'Super User',\n                    'Super Admin',\n                    'Admin',",
            "'Super User',\n                    'Super Admin',\n                    'Admin',\n                    'Administrator',",
            $controller
        );

        $controller = str_replace(
            "['Super User', 'Super Admin', 'Admin']",
            "['Super User', 'Super Admin', 'Admin', 'Administrator']",
            $controller
        );

        backupAndWrite($controllerPath, $controller, $root, $backupRoot);
    }
}

echo PHP_EOL;
echo "Sprint 6.4.7 berhasil diterapkan." . PHP_EOL;
echo "Backup: {$backupRoot}" . PHP_EOL;
echo PHP_EOL;
echo "Jalankan:" . PHP_EOL;
echo "php artisan view:clear" . PHP_EOL;
echo "php artisan optimize:clear" . PHP_EOL;
echo "php artisan view:cache" . PHP_EOL;
