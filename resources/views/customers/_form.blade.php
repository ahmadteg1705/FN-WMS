@php
    $editing = $customer->exists;
@endphp

<div class="grid gap-5 lg:grid-cols-2">
    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="mb-4 text-lg font-bold text-slate-800">Identitas Pelanggan</h2>

        <div class="space-y-4">
            <div>
                <label class="mb-1 block text-sm font-semibold">Nomor Pelanggan</label>

                <div class="flex gap-2">
                    <input
                        id="nomor_pelanggan"
                        name="nomor_pelanggan"
                        value="{{ old('nomor_pelanggan', $customer->nomor_pelanggan ?: ($suggestedCustomerNumber ?? '')) }}"
                        class="w-full rounded-lg border-slate-300"
                        placeholder="Isi manual atau klik Generate Otomatis">

                    @unless($editing)
                        <button
                            id="generate-customer-number"
                            type="button"
                            data-url="{{ route('customers.generate-number') }}"
                            class="shrink-0 rounded-lg bg-blue-50 px-4 py-2 text-sm font-bold text-blue-700 hover:bg-blue-100">
                            Generate Otomatis
                        </button>
                    @endunless
                </div>

                <p class="mt-1 text-xs text-slate-500">
                    Nomor dapat diisi manual agar sama dengan sistem pelanggan lama.
                    Nomor otomatis mengambil pola angka terakhir dari data pelanggan yang sudah ada.
                </p>

                @error('nomor_pelanggan')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Nama *</label>
                <input name="nama" value="{{ old('nama', $customer->nama) }}"
                       class="w-full rounded-lg border-slate-300" required>
                @error('nama')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">NIK *</label>
                <input name="nik" value="{{ old('nik', $customer->nik) }}"
                       class="w-full rounded-lg border-slate-300" required>
                @error('nik')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Alamat *</label>
                <textarea name="alamat" rows="4" class="w-full rounded-lg border-slate-300" required>{{ old('alamat', $customer->alamat) }}</textarea>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-semibold">Telepon *</label>
                    <input name="telepon" value="{{ old('telepon', $customer->telepon) }}"
                           class="w-full rounded-lg border-slate-300" required>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold">Email</label>
                    <input name="email" type="email" value="{{ old('email', $customer->email) }}"
                           class="w-full rounded-lg border-slate-300">
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-semibold">Tanggal Registrasi</label>
                    <input name="tanggal_registrasi" type="date"
                           value="{{ old('tanggal_registrasi', optional($customer->tanggal_registrasi)->format('Y-m-d') ?? $customer->tanggal_registrasi) }}"
                           class="w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold">Status *</label>
                    <select name="status" class="w-full rounded-lg border-slate-300" required>
                        @foreach(['Aktif', 'Nonaktif', 'Suspend', 'Menunggu Verifikasi'] as $option)
                            <option value="{{ $option }}" @selected(old('status', $customer->status ?: 'Aktif') === $option)>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="mb-4 text-lg font-bold text-slate-800">Layanan dan Jaringan</h2>

        <div class="space-y-4">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-semibold">Paket Internet</label>
                    <input name="paket" value="{{ old('paket', $customer->paket) }}"
                           class="w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold">Router NAS</label>
                    <input name="nas" value="{{ old('nas', $customer->nas) }}"
                           class="w-full rounded-lg border-slate-300">
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-semibold">ODP</label>
                    <input name="odp" value="{{ old('odp', $customer->odp) }}"
                           class="w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold">Nomor ONU</label>
                    <input name="onu_number" value="{{ old('onu_number', $customer->onu_number) }}"
                           class="w-full rounded-lg border-slate-300">
                </div>
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">SN Modem</label>
                <input name="sn_modem" value="{{ old('sn_modem', $customer->sn_modem) }}"
                       class="w-full rounded-lg border-slate-300 font-mono">
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-semibold">Username PPPoE</label>
                    <input name="pppoe_username" value="{{ old('pppoe_username', $customer->pppoe_username) }}"
                           class="w-full rounded-lg border-slate-300 font-mono">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold">Password PPPoE</label>
                    <input name="pppoe_password" value="{{ old('pppoe_password', $customer->pppoe_password) }}"
                           class="w-full rounded-lg border-slate-300 font-mono">
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-semibold">Latitude</label>
                    <input name="latitude" value="{{ old('latitude', $customer->latitude) }}"
                           class="w-full rounded-lg border-slate-300">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold">Longitude</label>
                    <input name="longitude" value="{{ old('longitude', $customer->longitude) }}"
                           class="w-full rounded-lg border-slate-300">
                </div>
            </div>
        </div>
    </section>

    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
        <h2 class="mb-4 text-lg font-bold text-slate-800">Foto dan Catatan</h2>

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-semibold">Foto KTP</label>
                <input name="foto_ktp" type="file" accept="image/*"
                       class="w-full rounded-lg border border-slate-300 p-2">
                @if($editing && $customer->foto_ktp)
                    <img src="{{ Storage::url($customer->foto_ktp) }}"
                         class="mt-3 h-36 w-full rounded-lg object-cover">
                @endif
            </div>

            <div>
                <label class="mb-1 block text-sm font-semibold">Foto Rumah</label>
                <input name="foto_rumah" type="file" accept="image/*"
                       class="w-full rounded-lg border border-slate-300 p-2">
                @if($editing && $customer->foto_rumah)
                    <img src="{{ Storage::url($customer->foto_rumah) }}"
                         class="mt-3 h-36 w-full rounded-lg object-cover">
                @endif
            </div>
        </div>

        <div class="mt-5">
            <label class="mb-1 block text-sm font-semibold">Catatan</label>
            <textarea name="catatan" rows="4" class="w-full rounded-lg border-slate-300">{{ old('catatan', $customer->catatan) }}</textarea>
        </div>
    </section>
</div>

<div class="mt-5 flex justify-end gap-3">
    <a href="{{ $editing ? route('customers.show', $customer) : route('customers.index') }}"
       class="rounded-lg border border-slate-300 bg-white px-5 py-3 font-semibold text-slate-700">
        Batal
    </a>
    <button class="rounded-lg bg-blue-600 px-6 py-3 font-bold text-white hover:bg-blue-700">
        {{ $editing ? 'Simpan Perubahan' : 'Tambah Pelanggan' }}
    </button>
</div>

@unless($editing)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const button = document.getElementById('generate-customer-number');
    const input = document.getElementById('nomor_pelanggan');

    if (!button || !input) {
        return;
    }

    button.addEventListener('click', async function () {
        const originalText = button.textContent;
        button.disabled = true;
        button.textContent = 'Memproses...';

        try {
            const response = await fetch(button.dataset.url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('Nomor pelanggan gagal dibuat.');
            }

            const data = await response.json();
            input.value = data.nomor_pelanggan || '';
            input.focus();
        } catch (error) {
            alert(error.message);
        } finally {
            button.disabled = false;
            button.textContent = originalText;
        }
    });
});
</script>
@endunless
