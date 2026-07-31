<?php
$root = __DIR__;
$backup = $root.'/storage/app/sprint_6_4_2_backup_'.date('Ymd_His');

function stopNow($m){fwrite(STDERR,"[GAGAL] $m\n");exit(1);}
function patch($path,$fn,$root,$backup){
    if(!is_file($path)) stopNow("File tidak ditemukan: $path");
    $old=file_get_contents($path); $new=$fn($old);
    if($new===$old){echo "[INFO] Tidak berubah: ".str_replace($root,'',$path)."\n";return;}
    $b=$backup.str_replace($root,'',$path); @mkdir(dirname($b),0777,true); copy($path,$b);
    file_put_contents($path,$new); echo "[OK] ".str_replace($root,'',$path)."\n";
}

/* View ODP */
patch($root.'/resources/views/odps/index.blade.php', function($c){
    if(!str_contains($c,"@can('odps.create')")){
        $c=str_replace(
            '            <a href="{{ route(\'odps.create\') }}"',
            "            @can('odps.create')\n            <a href=\"{{ route('odps.create') }}\"",
            $c
        );
        $c=str_replace(
            "                Tambah ODP\n            </a>",
            "                Tambah ODP\n            </a>\n            @endcan",
            $c
        );
    }

    if(!str_contains($c,"@can('odps.import')")){
        $c=str_replace(
            "            <button\n                type=\"button\"\n                onclick=\"openImportModal()\"",
            "            @can('odps.import')\n            <button\n                type=\"button\"\n                onclick=\"openImportModal()\"",
            $c
        );
        $c=str_replace(
            "                Import\n            </button>",
            "                Import\n            </button>\n            @endcan",
            $c
        );
        $c=str_replace(
            '<x-fn.import-modal />',
            "@can('odps.import')\n<x-fn.import-modal />\n@endcan",
            $c
        );
    }

    if(!str_contains($c,"@can('odps.export')")){
        $c=str_replace(
            '            <a href="{{ route(\'odps.export\') }}"',
            "            @can('odps.export')\n            <a href=\"{{ route('odps.export') }}\"",
            $c
        );
        $c=str_replace(
            "                Export\n            </a>",
            "                Export\n            </a>\n            @endcan",
            $c
        );
        $c=str_replace(
            "            <button\n                type=\"button\"\n                class=\"inline-flex items-center justify-center gap-2 px-3.5 py-2.5 bg-slate-100",
            "            @can('odps.export')\n            <button\n                type=\"button\"\n                onclick=\"window.print()\"\n                class=\"inline-flex items-center justify-center gap-2 px-3.5 py-2.5 bg-slate-100",
            $c
        );
        $c=str_replace(
            "                Print\n            </button>",
            "                Print\n            </button>\n            @endcan",
            $c
        );
    }

    if(!str_contains($c,"@can('odps.edit')")){
        $c=str_replace("    {{-- Edit --}}","@can('odps.edit')\n    {{-- Edit --}}",$c);
        $c=str_replace("    </a>\n\n    {{-- Hapus --}}","    </a>\n@endcan\n\n@can('odps.delete')\n    {{-- Hapus --}}",$c);
        $c=str_replace("    </form>\n</div>","    </form>\n@endcan\n</div>",$c);
    }

    return $c;
},$root,$backup);

/* Route protection */
patch($root.'/routes/web.php', function($c){
    $c=preg_replace("~\\s*Route::get\\('/odps/export'.*?->name\\('odps\\.export'\\);~s",'',$c);
    $c=preg_replace("~\\s*Route::get\\('/odps/template'.*?->name\\('odps\\.template'\\);~s",'',$c);
    $c=preg_replace("~\\s*Route::resource\\('odps',\\s*OdpController::class\\);~s",'',$c);
    $c=preg_replace("~\\s*Route::post\\('/odps/import'.*?->name\\('odps\\.import'\\);~s",'',$c);

    if(!str_contains($c,"permission:odps.view")){
        $block=<<<'PHP'

    Route::get('/odps', [OdpController::class, 'index'])
        ->middleware('permission:odps.view')->name('odps.index');
    Route::get('/odps/{odp}', [OdpController::class, 'show'])
        ->middleware('permission:odps.view')->name('odps.show');

    Route::get('/odps/create', [OdpController::class, 'create'])
        ->middleware('permission:odps.create')->name('odps.create');
    Route::post('/odps', [OdpController::class, 'store'])
        ->middleware('permission:odps.create')->name('odps.store');

    Route::get('/odps/{odp}/edit', [OdpController::class, 'edit'])
        ->middleware('permission:odps.edit')->name('odps.edit');
    Route::put('/odps/{odp}', [OdpController::class, 'update'])
        ->middleware('permission:odps.edit')->name('odps.update');
    Route::patch('/odps/{odp}', [OdpController::class, 'update'])
        ->middleware('permission:odps.edit');

    Route::delete('/odps/{odp}', [OdpController::class, 'destroy'])
        ->middleware('permission:odps.delete')->name('odps.destroy');

    Route::post('/odps/import', [OdpController::class, 'import'])
        ->middleware('permission:odps.import')->name('odps.import');
    Route::get('/odps/template', [OdpController::class, 'downloadTemplate'])
        ->middleware('permission:odps.import')->name('odps.template');

    Route::get('/odps/export', [OdpController::class, 'export'])
        ->middleware('permission:odps.export')->name('odps.export');

PHP;
        $needle="    Route::resource('routers', RouterController::class);";
        if(!str_contains($c,$needle)) stopNow('Posisi route Router NAS tidak ditemukan.');
        $c=str_replace($needle,$needle.$block,$c);
    }
    return $c;
},$root,$backup);

echo "\nSelesai. Backup: $backup\n";
echo "Jalankan:\n";
echo "php artisan permission:cache-reset\n";
echo "php artisan route:clear\n";
echo "php artisan view:clear\n";
echo "php artisan optimize:clear\n";
echo "php artisan route:list --path=odps\n";
