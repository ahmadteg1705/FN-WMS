<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\WorkOrderInstallation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class WorkOrderInstallationController extends Controller
{
    public function edit(WorkOrder $workOrder): View
    {
        abort_unless(
            $workOrder->status === 'Instalasi',
            403,
            'Dokumentasi hanya dapat diisi ketika Work Order berstatus Instalasi.'
        );

        $installation = WorkOrderInstallation::firstOrCreate([
            'work_order_id' => $workOrder->id,
        ]);

        $workOrder->load(['registration', 'team']);

        return view(
            'work_order_installations.edit',
            compact('workOrder', 'installation')
        );
    }

    public function update(Request $request, WorkOrder $workOrder): RedirectResponse
    {
        abort_unless(
            $workOrder->status === 'Instalasi',
            403,
            'Data instalasi hanya dapat disimpan ketika Work Order berstatus Instalasi.'
        );

        $installation = WorkOrderInstallation::firstOrCreate([
            'work_order_id' => $workOrder->id,
        ]);

        $this->saveInstallationData($request, $workOrder, $installation);

        return redirect()
            ->route('work-order-installation.edit', $workOrder)
            ->with('success', 'Data instalasi berhasil disimpan sementara.');
    }

    public function completeInstallation(
        Request $request,
        WorkOrder $workOrder
    ): RedirectResponse {
        abort_unless(
            $workOrder->status === 'Instalasi',
            403,
            'Instalasi hanya dapat diselesaikan ketika Work Order berstatus Instalasi.'
        );

        DB::transaction(function () use ($request, $workOrder): void {
            $installation = WorkOrderInstallation::firstOrCreate([
                'work_order_id' => $workOrder->id,
            ]);

            // Simpan perubahan terakhir dari form sebelum diperiksa kelengkapannya.
            $this->saveInstallationData($request, $workOrder, $installation);
            $installation->refresh();

            $missing = $this->getMissingCompletionItems($installation);

            if ($missing !== []) {
                throw ValidationException::withMessages([
                    'installation_complete' =>
                        'Instalasi belum dapat diselesaikan. Lengkapi: ' .
                        implode(', ', $missing) . '.',
                ]);
            }

            $installation->dikirim_verifikasi_at = now();
            $installation->save();

            $workOrder->status = 'Menunggu Verifikasi';
            $workOrder->save();

            if ($workOrder->registration) {
                $registration = $workOrder->registration;
                $oldStatus = $registration->status;

                $registration->status = 'Menunggu Verifikasi';
                $registration->save();

                DB::table('registration_histories')->insert([
                    'registration_id' => $registration->id,
                    'status_lama' => $oldStatus,
                    'status_baru' => 'Menunggu Verifikasi',
                    'catatan' => 'Teknisi menyelesaikan instalasi dan mengirim dokumentasi untuk verifikasi.',
                    'user_id' => auth()->id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return redirect()
            ->route('work-orders.show', $workOrder)
            ->with(
                'success',
                'Instalasi berhasil diselesaikan dan dikirim untuk verifikasi.'
            );
    }

    private function saveInstallationData(
        Request $request,
        WorkOrder $workOrder,
        WorkOrderInstallation $installation
    ): void {
        $validated = $request->validate([
            'sn_modem' => ['nullable', 'string', 'max:100'],
            'panjang_kabel' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'catatan_teknisi' => ['nullable', 'string', 'max:2000'],
            'foto_sn_modem' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'foto_speedtest' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'foto_rumah_depan' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'foto_form_registrasi' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'foto_redaman_modem' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $installation->sn_modem = $validated['sn_modem'] ?? null;
        $installation->panjang_kabel = $validated['panjang_kabel'] ?? null;
        $installation->latitude = $validated['latitude'] ?? null;
        $installation->longitude = $validated['longitude'] ?? null;
        $installation->catatan_teknisi = $validated['catatan_teknisi'] ?? null;

        $installation->modem_terpasang = $request->boolean('modem_terpasang');
        $installation->onu_online = $request->boolean('onu_online');
        $installation->internet_normal = $request->boolean('internet_normal');
        $installation->speedtest_berhasil = $request->boolean('speedtest_berhasil');
        $installation->pelanggan_menerima = $request->boolean('pelanggan_menerima');

        if (filled($installation->sn_modem) && is_null($installation->sn_disimpan_at)) {
            $installation->sn_disimpan_at = now();
        }

        $photoFields = [
            'foto_sn_modem',
            'foto_speedtest',
            'foto_rumah_depan',
            'foto_form_registrasi',
            'foto_redaman_modem',
        ];

        foreach ($photoFields as $field) {
            if (!$request->hasFile($field)) {
                continue;
            }

            $oldPhoto = $installation->{$field};
            $newPhoto = $this->storeInstallationPhotoAsWebp(
                file: $request->file($field),
                field: $field,
                workOrderId: $workOrder->id,
            );

            $installation->{$field} = $newPhoto;

            if (filled($oldPhoto) && $oldPhoto !== $newPhoto) {
                Storage::disk('public')->delete($oldPhoto);
            }
        }

        $installation->save();
    }

    private function getMissingCompletionItems(
        WorkOrderInstallation $installation
    ): array {
        $requirements = [
            'Foto SN modem' => filled($installation->foto_sn_modem),
            'Foto speedtest' => filled($installation->foto_speedtest),
            'Foto rumah tampak depan' => filled($installation->foto_rumah_depan),
            'Foto form registrasi' => filled($installation->foto_form_registrasi),
            'Foto redaman modem' => filled($installation->foto_redaman_modem),
            'Checklist modem terpasang' => (bool) $installation->modem_terpasang,
            'Checklist ONU online' => (bool) $installation->onu_online,
            'Checklist internet normal' => (bool) $installation->internet_normal,
            'Checklist speedtest berhasil' => (bool) $installation->speedtest_berhasil,
            'Checklist pelanggan menerima hasil' => (bool) $installation->pelanggan_menerima,
            'Lokasi GPS' => filled($installation->latitude) && filled($installation->longitude),
        ];

        return array_keys(array_filter(
            $requirements,
            static fn (bool $complete): bool => !$complete
        ));
    }

    private function storeInstallationPhotoAsWebp(
        UploadedFile $file,
        string $field,
        int $workOrderId
    ): string {
        if (!function_exists('imagewebp')) {
            throw ValidationException::withMessages([
                $field => 'Konversi WebP belum didukung oleh PHP.',
            ]);
        }

        $binary = file_get_contents($file->getRealPath());

        if ($binary === false) {
            throw ValidationException::withMessages([
                $field => 'File foto tidak dapat dibaca.',
            ]);
        }

        $sourceImage = @imagecreatefromstring($binary);

        if ($sourceImage === false) {
            throw ValidationException::withMessages([
                $field => 'Format atau isi foto tidak valid.',
            ]);
        }

        imagepalettetotruecolor($sourceImage);
        imagealphablending($sourceImage, true);
        imagesavealpha($sourceImage, true);

        $directory = 'installations/' . $workOrderId;
        Storage::disk('public')->makeDirectory($directory);

        $filename = $field . '_' . now()->format('Ymd_His') . '_' .
            Str::lower(Str::random(8)) . '.webp';

        $relativePath = $directory . '/' . $filename;
        $absolutePath = Storage::disk('public')->path($relativePath);
        $success = imagewebp($sourceImage, $absolutePath, 80);

        imagedestroy($sourceImage);

        if (!$success || !Storage::disk('public')->exists($relativePath)) {
            throw ValidationException::withMessages([
                $field => 'Foto gagal dikonversi dan disimpan sebagai WebP.',
            ]);
        }

        return $relativePath;
    }
}
