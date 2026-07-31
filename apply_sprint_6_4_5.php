<?php

$root = __DIR__;
$backupRoot = $root . '/storage/app/sprint_6_4_5_backup_' . date('Ymd_His');

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
        /*
         * Tambahkan helper agar user Marketing selalu terikat ke akun sendiri.
         */
        if (!str_contains($content, 'private function resolveMarketingId')) {
            $helper = <<<'PHP'

    private function resolveMarketingId(Request $request): int
    {
        if ($request->user()->hasRole('Marketing')) {
            return (int) $request->user()->id;
        }

        return (int) $request->validate([
            'marketing_id' => ['required', 'integer', 'exists:users,id'],
        ])['marketing_id'];
    }

    private function marketingUsersForForm(Request $request)
    {
        if ($request->user()->hasRole('Marketing')) {
            return \App\Models\User::query()
                ->whereKey($request->user()->id)
                ->get();
        }

        return \App\Models\User::role('Marketing')
            ->orderBy('name')
            ->get();
    }

PHP;

            $lastBrace = strrpos($content, '}');

            if ($lastBrace === false) {
                failPatch('Penutup RegistrationController tidak ditemukan.');
            }

            $content = substr($content, 0, $lastBrace)
                . $helper
                . substr($content, $lastBrace);
        }

        /*
         * Pada create/edit, paksa daftar marketing hanya akun login jika role Marketing.
         * Pola menangani assignment $marketings = ... yang sudah ada.
         */
        foreach (['create', 'edit'] as $method) {
            $methodPattern = '~(public\s+function\s+' . $method . '\s*\([^)]*\)\s*\{)([\s\S]*?)(?=\n\s*public\s+function|\n\s*private\s+function|\n\})~';

            if (!preg_match($methodPattern, $content, $match)) {
                failPatch("Method {$method} tidak ditemukan.");
            }

            $methodBody = $match[0];

            if (!str_contains($methodBody, 'marketingUsersForForm')) {
                $updatedBody = preg_replace(
                    '~\$marketings\s*=\s*[^;]+;~s',
                    '$marketings = $this->marketingUsersForForm($request);',
                    $methodBody,
                    1,
                    $count
                );

                if ($updatedBody === null || $count !== 1) {
                    failPatch("Assignment \$marketings pada method {$method} tidak ditemukan.");
                }

                /*
                 * Pastikan Request tersedia pada signature method.
                 */
                $updatedBody = preg_replace(
                    '~public\s+function\s+' . $method . '\s*\(\s*~',
                    'public function ' . $method . '(Request $request, ',
                    $updatedBody,
                    1
                );

                $updatedBody = str_replace('(Request $request, )', '(Request $request)', $updatedBody);

                $content = str_replace($methodBody, $updatedBody, $content);
            }
        }

        /*
         * Pada store/update, abaikan marketing_id dari browser untuk role Marketing.
         * Sisipkan setelah validasi agar nilai final selalu berasal dari akun login.
         */
        foreach (['store', 'update'] as $method) {
            $methodPattern = '~(public\s+function\s+' . $method . '\s*\([^)]*\)\s*\{)([\s\S]*?)(?=\n\s*public\s+function|\n\s*private\s+function|\n\})~';

            if (!preg_match($methodPattern, $content, $match)) {
                failPatch("Method {$method} tidak ditemukan.");
            }

            $methodBody = $match[0];

            if (str_contains($methodBody, 'resolveMarketingId')) {
                continue;
            }

            /*
             * Mendukung variabel validasi umum: $validated atau $data.
             */
            if (preg_match('~\$(validated|data)\s*=\s*\$request->validate\([\s\S]*?\);~', $methodBody, $validationMatch)) {
                $variable = '$' . $validationMatch[1];
                $replacement = $validationMatch[0]
                    . "\n\n        {$variable}['marketing_id'] = \$this->resolveMarketingId(\$request);";

                $updatedBody = str_replace($validationMatch[0], $replacement, $methodBody);
            } elseif (preg_match('~\$request->validate\([\s\S]*?\);~', $methodBody, $validationMatch)) {
                /*
                 * Jika controller memakai $request langsung, merge nilai aman.
                 */
                $replacement = $validationMatch[0]
                    . "\n\n        \$request->merge([\n"
                    . "            'marketing_id' => \$this->resolveMarketingId(\$request),\n"
                    . "        ]);";

                $updatedBody = str_replace($validationMatch[0], $replacement, $methodBody);
            } else {
                failPatch("Bagian validasi pada method {$method} tidak ditemukan.");
            }

            $content = str_replace($methodBody, $updatedBody, $content);
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

$patchedForm = false;

foreach ($formCandidates as $path) {
    if (!is_file($path)) {
        continue;
    }

    patchFile(
        $path,
        function (string $content) use (&$patchedForm): string {
            if (str_contains($content, 'marketing-auto-current-user')) {
                $patchedForm = true;
                return $content;
            }

            /*
             * Cari blok label Marketing sampai select penutup.
             */
            $pattern = <<<'REGEX'
~(?P<block><label[^>]*>\s*Marketing\s*</label>[\s\S]*?<select[^>]*name=["']marketing_id["'][\s\S]*?</select>)~
REGEX;

            if (!preg_match($pattern, $content, $match)) {
                return $content;
            }

            $adminBlock = $match['block'];

            $replacement = <<<'BLADE'
{{-- marketing-auto-current-user --}}
@role('Marketing')
    <label class="mb-1 block text-sm font-medium text-slate-700">Marketing</label>

    <input type="hidden" name="marketing_id" value="{{ auth()->id() }}">

    <div class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2.5 text-slate-700">
        {{ auth()->user()->name }}
    </div>

    <p class="mt-1 text-xs text-slate-500">
        Otomatis menggunakan akun Marketing yang sedang login.
    </p>
@else
ADMIN_BLOCK
@endrole
BLADE;

            $replacement = str_replace('ADMIN_BLOCK', $adminBlock, $replacement);
            $content = preg_replace($pattern, $replacement, $content, 1, $count);

            if ($count === 1) {
                $patchedForm = true;
            }

            return $content;
        },
        $root,
        $backupRoot
    );
}

if (!$patchedForm) {
    failPatch('Field dropdown Marketing tidak ditemukan pada form Registrasi.');
}

echo PHP_EOL;
echo "Sprint 6.4.5 berhasil diterapkan." . PHP_EOL;
echo "Backup: {$backupRoot}" . PHP_EOL;
echo PHP_EOL;
echo "Jalankan:" . PHP_EOL;
echo "php artisan view:clear" . PHP_EOL;
echo "php artisan route:clear" . PHP_EOL;
echo "php artisan optimize:clear" . PHP_EOL;
echo "php artisan view:cache" . PHP_EOL;
