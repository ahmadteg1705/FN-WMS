<?php
$root=__DIR__;
$backup=$root.'/storage/app/sprint_6_4_3_backup_'.date('Ymd_His');
function stopFix(string $m): never { fwrite(STDERR,"[GAGAL] $m\n"); exit(1); }
function patchFile(string $path, callable $fn, string $root, string $backup): void {
    if(!is_file($path)) stopFix("File tidak ditemukan: $path");
    $old=file_get_contents($path); $new=$fn($old);
    if($new===$old){ echo "[INFO] Tidak berubah: ".str_replace($root,'',$path)."\n"; return; }
    $b=$backup.str_replace($root,'',$path); @mkdir(dirname($b),0777,true); copy($path,$b);
    file_put_contents($path,$new); echo "[OK] ".str_replace($root,'',$path)."\n";
}
patchFile($root.'/resources/views/registrations/index.blade.php', function(string $c): string {
    $rules = [
        ["registrations.create", '~(\\s*)(<a href="\\{\\{ route\\(\\\'registrations\\.create\\\'\\) \\}\\}".*?</a>)~s'],
        ["registrations.export", '~(\\s*)(<a href="\\{\\{ route\\(\\\'reports\\.registrations\\.excel\\\', request\\(\\)->query\\(\\)\\) \\}\\}".*?</a>)~s'],
        ["registrations.show", '~(\\s*)(<a href="\\{\\{ route\\(\\\'registrations\\.show\\\', \\$registration\\) \\}\\}".*?</a>)~s'],
        ["registrations.edit", '~(\\s*)(<a href="\\{\\{ route\\(\\\'registrations\\.edit\\\', \\$registration\\) \\}\\}".*?</a>)~s'],
        ["registrations.delete", '~(\\s*)(<form action="\\{\\{ route\\(\\\'registrations\\.destroy\\\', \\$registration\\) \\}\\}".*?</form>)~s'],
    ];
    foreach($rules as [$permission,$pattern]){
        if(str_contains($c,"@can('$permission')")) continue;
        $replacement='$1@can(\''.$permission.'\')$1$2$1@endcan';
        $updated=preg_replace($pattern,$replacement,$c,1,$count);
        if($updated===null) stopFix("Regex Registrasi gagal: $permission");
        if($count===0) echo "[PERINGATAN] Tombol tidak ditemukan: $permission\n";
        $c=$updated;
    }
    return $c;
},$root,$backup);
patchFile($root.'/routes/web.php', function(string $c): string {
    $c=preg_replace("~\\s*Route::get\\('/reports/registrations/excel'.*?->name\\('reports\\.registrations\\.excel'\\);~s",'', $c);
    $c=preg_replace("~\\s*Route::get\\('/odps/export'.*?->name\\('odps\\.export'\\);~s",'', $c);
    $c=preg_replace("~\\s*Route::get\\('/odps/template'.*?->name\\('odps\\.template'\\);~s",'', $c);
    $c=preg_replace("~\\s*Route::resource\\('odps',\\s*OdpController::class\\);~s",'', $c);
    if(!str_contains($c,"permission:odps.view")){
        $block=<<<'PHP'

    Route::get('/odps', [OdpController::class, 'index'])->middleware('permission:odps.view')->name('odps.index');
    Route::get('/odps/create', [OdpController::class, 'create'])->middleware('permission:odps.create')->name('odps.create');
    Route::post('/odps', [OdpController::class, 'store'])->middleware('permission:odps.create')->name('odps.store');
    Route::get('/odps/{odp}/edit', [OdpController::class, 'edit'])->middleware('permission:odps.edit')->name('odps.edit');
    Route::match(['put','patch'], '/odps/{odp}', [OdpController::class, 'update'])->middleware('permission:odps.edit')->name('odps.update');
    Route::delete('/odps/{odp}', [OdpController::class, 'destroy'])->middleware('permission:odps.delete')->name('odps.destroy');
    Route::post('/odps/import', [OdpController::class, 'import'])->middleware('permission:odps.import')->name('odps.import');
    Route::get('/odps/template', [OdpController::class, 'downloadTemplate'])->middleware('permission:odps.import')->name('odps.template');
    Route::get('/odps/export', [OdpController::class, 'export'])->middleware('permission:odps.export')->name('odps.export');
PHP;
        $needle="    Route::resource('routers', RouterController::class);";
        if(!str_contains($c,$needle)) stopFix('Posisi route Router NAS tidak ditemukan.');
        $c=str_replace($needle,$needle.$block,$c);
    }
    $c=preg_replace("~\\s*Route::resource\\('registrations',\\s*RegistrationController::class\\);~s",'', $c);
    if(!str_contains($c,"permission:registrations.view")){
        $block=<<<'PHP'

    Route::get('/registrations', [RegistrationController::class, 'index'])->middleware('permission:registrations.view')->name('registrations.index');
    Route::get('/registrations/create', [RegistrationController::class, 'create'])->middleware('permission:registrations.create')->name('registrations.create');
    Route::post('/registrations', [RegistrationController::class, 'store'])->middleware('permission:registrations.create')->name('registrations.store');
    Route::get('/registrations/{registration}', [RegistrationController::class, 'show'])->middleware('permission:registrations.show')->name('registrations.show');
    Route::get('/registrations/{registration}/edit', [RegistrationController::class, 'edit'])->middleware('permission:registrations.edit')->name('registrations.edit');
    Route::match(['put','patch'], '/registrations/{registration}', [RegistrationController::class, 'update'])->middleware('permission:registrations.edit')->name('registrations.update');
    Route::delete('/registrations/{registration}', [RegistrationController::class, 'destroy'])->middleware('permission:registrations.delete')->name('registrations.destroy');
    Route::get('/reports/registrations/excel', [ReportController::class, 'registrationExcel'])->middleware('permission:registrations.export')->name('reports.registrations.excel');
PHP;
        $needle="    Route::resource('marketings', MarketingController::class);";
        if(!str_contains($c,$needle)) stopFix('Posisi route Marketing tidak ditemukan.');
        $c=str_replace($needle,$needle.$block,$c);
    }
    return $c;
},$root,$backup);
echo "\nSelesai. Backup: $backup\n";
echo "php artisan permission:cache-reset\nphp artisan route:clear\nphp artisan view:clear\nphp artisan optimize:clear\nphp artisan view:cache\nphp artisan route:list --path=odps\nphp artisan route:list --path=registrations\n";
