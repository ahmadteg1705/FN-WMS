<?php

/**
 * Sprint 6.3.1 Final Full v2
 * Adaptive installer: tidak bergantung pada spasi/format file yang persis sama.
 *
 * Jalankan:
 * php apply_sprint_6_3_1_v2.php
 */

$root = __DIR__;
$timestamp = date('Ymd_His');
$backupRoot = $root . '/storage/app/sprint_6_3_1_backup_' . $timestamp;

function stopPatch(string $message): never
{
    fwrite(STDERR, "[GAGAL] {$message}\n");
    exit(1);
}

function readRequired(string $path): string
{
    if (!is_file($path)) {
        stopPatch("File tidak ditemukan: {$path}");
    }

    $value = file_get_contents($path);
    if ($value === false) {
        stopPatch("File tidak dapat dibaca: {$path}");
    }

    return $value;
}

function saveBackup(string $path, string $content, string $root, string $backupRoot): void
{
    $relative = ltrim(str_replace($root, '', $path), DIRECTORY_SEPARATOR);
    $backup = $backupRoot . DIRECTORY_SEPARATOR . $relative;

    if (!is_dir(dirname($backup))) {
        mkdir(dirname($backup), 0777, true);
    }

    if (!copy($path, $backup)) {
        stopPatch("Gagal membuat backup {$relative}");
    }

    if (file_put_contents($path, $content) === false) {
        stopPatch("Gagal menulis {$relative}");
    }

    echo "[OK] {$relative}\n";
}

function replaceRegex(
    string $content,
    string $pattern,
    string $replacement,
    string $label,
    bool $required = true
): string {
    $result = preg_replace($pattern, $replacement, $content, 1, $count);

    if ($result === null) {
        stopPatch("Regex tidak valid pada {$label}");
    }

    if ($required && $count === 0) {
        stopPatch("Bagian {$label} tidak ditemukan. Backup tidak ditimpa.");
    }

    if ($count > 0) {
        echo "[UBAH] {$label}\n";
    }

    return $result;
}

/*
|--------------------------------------------------------------------------
| A. ROUTES
|--------------------------------------------------------------------------
*/
$path = $root . '/routes/web.php';
$content = readRequired($path);

/* Hapus blok Aktivasi NOC kedua yang hanya berisi index + accept */
$content = replaceRegex(
    $content,
    '~\s*/\*\s*\n\s*\|[-| ]+\n\s*\|\s*Modul Aktivasi NOC\s*\n.*?Route::prefix\([\'"]activation[\'"]\).*?Route::post\([\'"]/\{nocActivation\}/accept[\'"].*?->name\([\'"]accept[\'"]\);\s*\}\);\s*~s',
    "\n",
    'route Aktivasi NOC duplikat',
    false
);

/* Alternatif bila komentar sudah berubah */
$activationCount = preg_match_all(
    '~Route::(?:middleware\([^;]+?\)\s*->\s*)?prefix\([\'"]activation[\'"]\)|Route::prefix\([\'"]activation[\'"]\)~s',
    $content
);

if ($activationCount > 1) {
    $content = replaceRegex(
        $content,
        '~\s*Route::prefix\([\'"]activation[\'"]\)\s*->name\([\'"]noc-activations\.[\'"]\)\s*->middleware\([\'"]permission:noc-activations\.view[\'"]\)\s*->group\(function\s*\(\)\s*\{.*?\}\);\s*(?=Route::middleware\([\'"]permission:users\.view[\'"]\))~s',
        "\n    ",
        'route Aktivasi NOC duplikat alternatif',
        false
    );
}

saveBackup($path, $content, $root, $backupRoot);

/*
|--------------------------------------------------------------------------
| B. DETAIL WORK ORDER / PPPoE
|--------------------------------------------------------------------------
*/
$path = $root . '/resources/views/work_orders/show.blade.php';
$content = readRequired($path);

$pppoeCard = <<<'BLADE'
@role('Super User|Super Admin|Admin|NOC')
<div class="rounded-lg border border-blue-100 bg-blue-50/60 p-4">
    <div class="mb-3 flex items-center justify-between gap-3">
        <div>
            <h3 class="font-semibold text-slate-800">Akun PPPoE</h3>
            <p class="mt-1 text-xs text-slate-500">
                Username dan password ini digunakan pada proses Aktivasi NOC.
            </p>
        </div>

        @role('Super User|Super Admin|Admin')
        <button
            type="button"
            onclick="openPPPoEModal()"
            class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-blue-700">
            {{ $workOrder->account ? '✏️ Edit Akun' : '➕ Tambah Akun' }}
        </button>
        @endrole
    </div>

    @if($workOrder->account)
        <div class="space-y-3 rounded-lg border border-blue-100 bg-white p-3">
            <div class="flex items-start justify-between gap-4">
                <span class="text-slate-500">Username</span>
                <span class="break-all text-right font-mono font-semibold text-slate-900">
                    {{ $workOrder->account->username }}
                </span>
            </div>

            <div class="flex items-start justify-between gap-4">
                <span class="text-slate-500">Password</span>
                <div class="text-right">
                    <span id="passwordText" class="break-all font-mono font-semibold text-slate-900">
                        ••••••••••
                    </span>
                    <br>
                    <button
                        type="button"
                        onclick="togglePassword()"
                        class="mt-1 text-xs font-semibold text-blue-600 hover:underline">
                        👁 Tampilkan / Sembunyikan
                    </button>
                </div>
            </div>
        </div>
    @else
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
            Akun PPPoE belum dibuat. Admin wajib melengkapinya sebelum Aktivasi NOC.
        </div>
    @endif
</div>
@endrole
BLADE;

if (!str_contains($content, 'Username dan password ini digunakan pada proses Aktivasi NOC.')) {
    /*
     * Cari blok role yang di dalamnya mengandung judul Akun PPPoE.
     * Tidak bergantung pada indentasi, baris kosong, maupun role lama.
     */
    $content = replaceRegex(
        $content,
        '~@role\([^\r\n]*\)\s*<div\b.*?<h3\b[^>]*>\s*Akun PPPoE\s*</h3>.*?</div>\s*@endrole~s',
        $pppoeCard,
        'kartu Akun PPPoE'
    );
} else {
    echo "[INFO] Kartu PPPoE sudah versi Sprint 6.3.1.\n";
}

/* Buka modal otomatis bila validasi akun gagal */
if (
    !str_contains($content, "old('work_order_id') !== null")
    && str_contains($content, 'function openPPPoEModal')
) {
    $autoOpen = <<<'BLADE'
<script>
document.addEventListener('DOMContentLoaded', function () {
    @if($errors->any() && (old('username') !== null || old('work_order_id') !== null))
        openPPPoEModal();
    @endif
});

BLADE;

    $content = replaceRegex(
        $content,
        '~<script>\s*(?=function\s+openPPPoEModal\s*\()~s',
        $autoOpen,
        'modal otomatis saat validasi gagal'
    );
}

saveBackup($path, $content, $root, $backupRoot);

/*
|--------------------------------------------------------------------------
| C. SIDEBAR: AKTIVASI NOC + PROSES AKTIVASI
|--------------------------------------------------------------------------
*/
$path = $root . '/resources/views/layouts/sidebar.blade.php';
$content = readRequired($path);

if (!str_contains($content, 'Proses Aktivasi')) {
    $processMenu = <<<'BLADE'

        @can('noc-activations.process')
        <a href="{{ route('noc-activations.index', ['status' => \App\Models\NocActivation::STATUS_PROCESSING]) }}"
            class="flex items-center gap-3 py-2 {{ request('status') === \App\Models\NocActivation::STATUS_PROCESSING || request()->routeIs('noc-activations.process') ? 'text-white font-semibold' : 'text-blue-200 hover:text-white transition' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ request('status') === \App\Models\NocActivation::STATUS_PROCESSING || request()->routeIs('noc-activations.process') ? 'bg-violet-300' : 'bg-blue-400/50' }}"></span>

            Proses Aktivasi

            @php
                $processingActivation = \App\Models\NocActivation::whereIn('status', [
                    \App\Models\NocActivation::STATUS_ACCEPTED,
                    \App\Models\NocActivation::STATUS_PROCESSING,
                ])->count();
            @endphp

            @if($processingActivation > 0)
                <span class="ml-auto inline-flex h-6 min-w-[24px] items-center justify-center rounded-full bg-violet-500 px-2 text-xs font-bold text-white">
                    {{ $processingActivation }}
                </span>
            @endif
        </a>
        @endcan

BLADE;

    /*
     * Sisipkan setelah link Aktivasi NOC dan sebelum menu Jadwal.
     * Anchor dicari dari route noc-activations.index agar tahan terhadap format.
     */
    $content = replaceRegex(
        $content,
        '~(@can\([\'"]noc-activations\.view[\'"]\).*?<a\b[^>]*href="\{\{\s*route\([\'"]noc-activations\.index[\'"]\)\s*\}\}".*?</a>\s*@endcan)(\s*@can\([\'"]schedules\.view[\'"]\))~s',
        '$1' . $processMenu . '$2',
        'menu sidebar Proses Aktivasi'
    );
} else {
    echo "[INFO] Menu Proses Aktivasi sudah ada.\n";
}

saveBackup($path, $content, $root, $backupRoot);

/*
|--------------------------------------------------------------------------
| D. VALIDASI DATA PPPoE SEBELUM PROSES NOC
|--------------------------------------------------------------------------
*/
$path = $root . '/app/Http/Controllers/NocActivationController.php';
$content = readRequired($path);

if (!str_contains($content, 'Akun PPPoE belum diisi oleh Admin')) {
    $guard = <<<'PHP'
        abort_unless(
            $nocActivation->workOrder?->account
                && filled($nocActivation->workOrder->account->username)
                && filled($nocActivation->workOrder->account->password),
            422,
            'Akun PPPoE belum diisi oleh Admin. Silakan minta Admin melengkapi username dan password pada Detail Work Order.'
        );

PHP;

    $content = replaceRegex(
        $content,
        '~(\s*)(\$registration\s*=\s*\$nocActivation->workOrder\?->registration;)~',
        '$1' . $guard . '$1$2',
        'validasi akun PPPoE sebelum proses Aktivasi NOC'
    );
} else {
    echo "[INFO] Validasi PPPoE di NOC sudah ada.\n";
}

saveBackup($path, $content, $root, $backupRoot);

echo "\n========================================\n";
echo "SPRINT 6.3.1 BERHASIL DITERAPKAN\n";
echo "========================================\n";
echo "Backup: {$backupRoot}\n\n";
echo "Jalankan:\n";
echo "php artisan permission:cache-reset\n";
echo "php artisan optimize:clear\n";
echo "php artisan route:list --path=activation\n";
