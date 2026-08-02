<?php
$root = __DIR__;
$backupRoot = $root . '/storage/app/sprint_6_4_6_backup_' . date('Ymd_His');

function failPatch(string $message): never {
    fwrite(STDERR, "[GAGAL] {$message}\n");
    exit(1);
}

function patchFile(string $path, callable $callback, string $root, string $backupRoot): void {
    if (!is_file($path)) failPatch("File tidak ditemukan: {$path}");
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

patchFile($root . '/app/Http/Controllers/RegistrationController.php', function (string $content): string {
    $oldHelperPattern = '~private\s+function\s+resolveMarketingId\s*\(Request\s+\$request\)\s*:\s*int\s*\{[\s\S]*?\n\s*\}\s*\n\s*private\s+function\s+marketingUsersForForm\s*\(Request\s+\$request\)\s*\{[\s\S]*?\n\s*\}\s*~';

    $newHelper = <<<'PHP'
private function resolveMarketingId(Request $request): int
    {
        if ($request->user()->hasRole('Marketing')) {
            $marketingId = $request->user()->marketing?->id;

            abort_unless(
                $marketingId,
                422,
                'Akun Marketing ini belum terhubung dengan data Marketing. Hubungi Admin.'
            );

            return (int) $marketingId;
        }

        return (int) $request->validate([
            'marketing_id' => ['required', 'integer', 'exists:marketings,id'],
        ])['marketing_id'];
    }

    private function marketingUsersForForm(Request $request)
    {
        if ($request->user()->hasRole('Marketing')) {
            return \App\Models\Marketing::query()
                ->with('user')
                ->where('user_id', $request->user()->id)
                ->get();
        }

        return \App\Models\Marketing::with('user')
            ->get()
            ->sortBy(fn ($marketing) => $marketing->user->name ?? '');
    }

PHP;

    $updated = preg_replace($oldHelperPattern, $newHelper, $content, 1, $count);

    if ($updated === null) failPatch('Regex helper Marketing tidak valid.');

    if ($count > 0) {
        $content = $updated;
    } elseif (!str_contains($content, 'private function resolveMarketingId')) {
        $lastBrace = strrpos($content, '}');
        if ($lastBrace === false) failPatch('Penutup controller tidak ditemukan.');
        $content = substr($content, 0, $lastBrace)
            . "\n    " . str_replace("\n", "\n    ", trim($newHelper)) . "\n"
            . substr($content, $lastBrace);
    }

    $content = preg_replace(
        "~'marketing_id'\s*=>\s*\\$request->marketing_id~",
        "'marketing_id' => \\$this->resolveMarketingId(\\$request)",
        $content
    );

    return $content;
}, $root, $backupRoot);

$formCandidates = [
    $root . '/resources/views/registrations/create.blade.php',
    $root . '/resources/views/registrations/edit.blade.php',
    $root . '/resources/views/registrations/_form.blade.php',
    $root . '/resources/views/registrations/form.blade.php',
];

$found = false;

foreach ($formCandidates as $path) {
    if (!is_file($path)) continue;

    patchFile($path, function (string $content) use (&$found): string {
        if (!str_contains($content, 'marketing-auto-current-user')) return $content;

        $found = true;
        $content = str_replace(
            'value="{{ auth()->id() }}"',
            'value="{{ auth()->user()->marketing?->id }}"',
            $content
        );

        if (!str_contains($content, 'Akun belum terhubung ke data Marketing')) {
            $content = str_replace(
                '<input type="hidden" name="marketing_id" value="{{ auth()->user()->marketing?->id }}">',
                <<<'BLADE'
@if(auth()->user()->marketing)
    <input type="hidden" name="marketing_id" value="{{ auth()->user()->marketing->id }}">
@else
    <div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-700">
        Akun belum terhubung ke data Marketing. Hubungi Admin.
    </div>
@endif
BLADE,
                $content
            );
        }

        return $content;
    }, $root, $backupRoot);
}

if (!$found) failPatch('Blok Marketing otomatis Sprint 6.4.5 tidak ditemukan.');

echo PHP_EOL;
echo "Sprint 6.4.6 berhasil diterapkan." . PHP_EOL;
echo "Backup: {$backupRoot}" . PHP_EOL;
echo "Jalankan:" . PHP_EOL;
echo "php artisan view:clear" . PHP_EOL;
echo "php artisan route:clear" . PHP_EOL;
echo "php artisan optimize:clear" . PHP_EOL;
echo "php artisan view:cache" . PHP_EOL;
