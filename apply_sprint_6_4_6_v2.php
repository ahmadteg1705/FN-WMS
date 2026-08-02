<?php

$root = __DIR__;
$backupRoot = $root . '/storage/app/sprint_6_4_6_v2_backup_' . date('Ymd_His');

function failPatch(string $message): never
{
    fwrite(STDERR, "[GAGAL] {$message}" . PHP_EOL);
    exit(1);
}

function patchFile(
    string $path,
    callable $callback,
    string $root,
    string $backupRoot
): void {
    if (!is_file($path)) {
        failPatch("File tidak ditemukan: {$path}");
    }

    $old = file_get_contents($path);

    if ($old === false) {
        failPatch("File tidak dapat dibaca: {$path}");
    }

    $new = $callback($old);

    if ($new === $old) {
        echo "[INFO] Tidak ada perubahan: "
            . str_replace($root, '', $path)
            . PHP_EOL;
        return;
    }

    $backup = $backupRoot . str_replace($root, '', $path);
    @mkdir(dirname($backup), 0777, true);

    if (!copy($path, $backup)) {
        failPatch("Gagal membuat backup: {$path}");
    }

    if (file_put_contents($path, $new) === false) {
        failPatch("Tidak dapat menulis: {$path}");
    }

    echo "[OK] " . str_replace($root, '', $path) . PHP_EOL;
}

/*
|--------------------------------------------------------------------------
| 1. RegistrationController
|--------------------------------------------------------------------------
*/
patchFile(
    $root . '/app/Http/Controllers/RegistrationController.php',
    function (string $content): string {
        $helperPattern = <<<'REGEX'
~\s*private\s+function\s+resolveMarketingId\s*\(Request\s+\$request\)\s*:\s*int\s*\{[\s\S]*?\n\s*\}\s*\n\s*private\s+function\s+marketingUsersForForm\s*\(Request\s+\$request\)\s*\{[\s\S]*?\n\s*\}\s*~
REGEX;

        $correctHelpers = <<<'PHP'

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
            'marketing_id' => [
                'required',
                'integer',
                'exists:marketings,id',
            ],
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

        return \App\Models\Marketing::query()
            ->with('user')
            ->get()
            ->sortBy(fn ($marketing) => $marketing->user->name ?? '');
    }

PHP;

        $updated = preg_replace(
            $helperPattern,
            $correctHelpers,
            $content,
            1,
            $helperCount
        );

        if ($updated === null) {
            failPatch('Pola helper Marketing tidak valid.');
        }

        if ($helperCount === 0) {
            $lastBrace = strrpos($content, '}');

            if ($lastBrace === false) {
                failPatch('Penutup RegistrationController tidak ditemukan.');
            }

            $content = substr($content, 0, $lastBrace)
                . $correctHelpers
                . substr($content, $lastBrace);
        } else {
            $content = $updated;
        }

        /*
         * Paksa marketing_id yang benar sebelum validasi/simpan.
         * Tidak melakukan interpolasi $this/$request di installer.
         */
        foreach (['store', 'update'] as $method) {
            $alreadyPatchedPattern =
                '~public\s+function\s+' . preg_quote($method, '~')
                . '\s*\([^)]*\)\s*\{\s*'
                . '\$request->merge\(\[\s*'
                . '\'marketing_id\'\s*=>\s*'
                . '\$this->resolveMarketingId\(\$request\)~s';

            if (preg_match($alreadyPatchedPattern, $content)) {
                continue;
            }

            $methodPattern =
                '~(public\s+function\s+' . preg_quote($method, '~')
                . '\s*\([^)]*\)\s*\{)~s';

            $injection = <<<'PHP'
$1
        $request->merge([
            'marketing_id' => $this->resolveMarketingId($request),
        ]);
PHP;

            $updated = preg_replace(
                $methodPattern,
                $injection,
                $content,
                1,
                $methodCount
            );

            if ($updated === null || $methodCount !== 1) {
                failPatch("Method {$method} tidak ditemukan.");
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
| 2. Form Registrasi
|--------------------------------------------------------------------------
*/
$formCandidates = [
    $root . '/resources/views/registrations/create.blade.php',
    $root . '/resources/views/registrations/edit.blade.php',
    $root . '/resources/views/registrations/_form.blade.php',
    $root . '/resources/views/registrations/form.blade.php',
];

$formFound = false;

foreach ($formCandidates as $path) {
    if (!is_file($path)) {
        continue;
    }

    patchFile(
        $path,
        function (string $content) use (&$formFound): string {
            if (!str_contains($content, 'marketing-auto-current-user')) {
                return $content;
            }

            $formFound = true;

            $content = str_replace(
                'value="{{ auth()->id() }}"',
                'value="{{ auth()->user()->marketing?->id }}"',
                $content
            );

            $content = str_replace(
                'value="{{ auth()->user()->marketing?->id }}"',
                'value="{{ auth()->user()->marketing->id }}"',
                $content
            );

            if (
                !str_contains(
                    $content,
                    'Akun belum terhubung ke data Marketing'
                )
            ) {
                $hidden = <<<'BLADE'
<input type="hidden" name="marketing_id" value="{{ auth()->user()->marketing->id }}">
BLADE;

                $replacement = <<<'BLADE'
@if(auth()->user()->marketing)
    <input type="hidden" name="marketing_id" value="{{ auth()->user()->marketing->id }}">
@else
    <div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-700">
        Akun belum terhubung ke data Marketing. Hubungi Admin.
    </div>
@endif
BLADE;

                $content = str_replace(
                    $hidden,
                    $replacement,
                    $content
                );
            }

            return $content;
        },
        $root,
        $backupRoot
    );
}

if (!$formFound) {
    failPatch(
        'Blok Marketing otomatis Sprint 6.4.5 tidak ditemukan pada form Registrasi.'
    );
}

echo PHP_EOL;
echo "Sprint 6.4.6 v2 berhasil diterapkan." . PHP_EOL;
echo "Backup: {$backupRoot}" . PHP_EOL;
echo PHP_EOL;
echo "Jalankan:" . PHP_EOL;
echo "php artisan view:clear" . PHP_EOL;
echo "php artisan route:clear" . PHP_EOL;
echo "php artisan optimize:clear" . PHP_EOL;
echo "php artisan view:cache" . PHP_EOL;
