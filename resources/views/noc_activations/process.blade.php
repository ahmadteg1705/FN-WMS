<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0f172a">
    <title>Proses Aktivasi NOC</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
<header class="sticky top-0 z-40 border-b border-slate-800 bg-slate-950 text-white shadow-lg">
    <div class="mx-auto flex max-w-7xl items-center gap-3 px-4 py-3 sm:px-6">
        <a href="{{ route('noc-activations.index') }}" class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/10 text-xl hover:bg-white/20">←</a>
        <div class="min-w-0 flex-1">
            <h1 class="truncate text-base font-bold sm:text-lg">Proses Aktivasi NOC</h1>
            <p class="truncate text-xs text-slate-400">WO {{ $nocActivation->workOrder?->work_order_no ?? '#'.$nocActivation->work_order_id }}</p>
        </div>
        <span class="rounded-full bg-violet-400/15 px-3 py-1 text-xs font-semibold text-violet-300">{{ $nocActivation->status }}</span>
    </div>
</header>

<main class="mx-auto max-w-7xl px-4 py-5 sm:px-6">
    @if(session('success'))
        <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <p class="font-bold">Data belum dapat disimpan:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form id="activation-form" action="{{ route('noc-activations.complete', $nocActivation) }}" method="POST">
        @csrf

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach([
                ['NAS', $data['nas']],
                ['Nama Pelanggan', $data['customer_name']],
                ['SN', $data['sn']],
                ['ODP', $data['odp']],
                ['Username', $data['username']],
                ['Password', $data['password']],
            ] as [$label, $value])
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $label }}</p>
                    <p class="mt-2 break-all font-mono text-sm font-bold text-slate-900">{{ $value }}</p>
                </div>
            @endforeach
        </section>

        <section class="mt-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="grid gap-4 md:grid-cols-[1fr_auto_auto] md:items-end">
                <div>
                    <label for="onu_number" class="mb-2 block text-sm font-bold text-slate-700">Nomor ONU <span class="text-red-600">*</span></label>
                    <input
                        id="onu_number"
                        name="onu_number"
                        type="number"
                        min="1"
                        max="128"
                        value="{{ old('onu_number', $nocActivation->onu_number) }}"
                        placeholder="Contoh: 25"
                        required
                        class="h-12 w-full rounded-xl border-slate-300 px-4 font-mono text-sm focus:border-cyan-500 focus:ring-cyan-500"
                    >
                </div>
                <button id="btn-check" type="button" class="h-12 rounded-xl bg-amber-500 px-5 text-sm font-bold text-white hover:bg-amber-600">Cek ONU Kosong</button>
                <button id="btn-generate" type="button" class="h-12 rounded-xl bg-cyan-600 px-5 text-sm font-bold text-white hover:bg-cyan-700">Generate</button>
            </div>
        </section>

        <input type="hidden" name="odp_name" value="{{ $data['odp'] }}">
        <textarea id="provisioning_script" name="provisioning_script" class="hidden">{{ old('provisioning_script', $nocActivation->provisioning_script) }}</textarea>

        <section class="mt-5 grid gap-5 xl:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="font-bold text-slate-900">Frame Cek ONU Kosong</h2>
                        <p class="mt-1 text-xs text-slate-500">Salin dan jalankan pada OLT.</p>
                    </div>
                    <button type="button" data-copy="check-frame" class="rounded-lg bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200">Copy</button>
                </div>
                <pre id="check-frame" class="mt-4 min-h-32 overflow-auto whitespace-pre-wrap rounded-xl bg-slate-950 p-4 font-mono text-sm text-emerald-300">Klik “Cek ONU Kosong”.</pre>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="font-bold text-slate-900">Frame 1 — Registrasi SN</h2>
                        <p class="mt-1 text-xs text-slate-500">Perintah registrasi ONU.</p>
                    </div>
                    <button type="button" data-copy="frame-1" class="rounded-lg bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200">Copy</button>
                </div>
                <pre id="frame-1" class="mt-4 min-h-48 overflow-auto whitespace-pre-wrap rounded-xl bg-slate-950 p-4 font-mono text-sm text-cyan-300">Klik “Generate”.</pre>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-2">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="font-bold text-slate-900">Frame 2 — Konfigurasi Layanan</h2>
                        <p class="mt-1 text-xs text-slate-500">Konfigurasi PPPoE, VLAN, dan manajemen ONU.</p>
                    </div>
                    <button type="button" data-copy="frame-2" class="rounded-lg bg-slate-100 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-200">Copy</button>
                </div>
                <pre id="frame-2" class="mt-4 min-h-64 overflow-auto whitespace-pre-wrap rounded-xl bg-slate-950 p-4 font-mono text-sm text-violet-300">Klik “Generate”.</pre>
            </div>
        </section>

        <section class="mt-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <label for="noc_notes" class="mb-2 block text-sm font-bold text-slate-700">Catatan NOC</label>
            <textarea id="noc_notes" name="noc_notes" rows="3" class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm focus:border-cyan-500 focus:ring-cyan-500" placeholder="Opsional">{{ old('noc_notes', $nocActivation->noc_notes) }}</textarea>

            <div class="mt-5 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('noc-activations.index') }}" class="flex h-12 items-center justify-center rounded-xl border border-slate-300 px-6 text-sm font-bold text-slate-700 hover:bg-slate-50">Kembali</a>
                <button type="submit" class="h-12 rounded-xl bg-emerald-600 px-7 text-sm font-bold text-white hover:bg-emerald-700" onclick="return confirm('Pastikan aktivasi sudah berhasil. Simpan dan kirim ke verifikasi Admin?')">
                    Simpan / Aktivasi Selesai
                </button>
            </div>
        </section>
    </form>
</main>

<script>
(() => {
    const data = @json($data);
    const onuInput = document.getElementById('onu_number');
    const checkFrame = document.getElementById('check-frame');
    const frame1 = document.getElementById('frame-1');
    const frame2 = document.getElementById('frame-2');
    const scriptInput = document.getElementById('provisioning_script');

    const sanitizeName = (value) => String(value || '')
        .trim()
        .replace(/\s+/g, '-')
        .replace(/[^a-zA-Z0-9._-]/g, '');

    const validateOnu = () => {
        const onu = Number(onuInput.value);
        if (!Number.isInteger(onu) || onu < 1 || onu > 128) {
            alert('Nomor ONU harus berupa angka 1 sampai 128.');
            onuInput.focus();
            return null;
        }
        return onu;
    };

    document.getElementById('btn-check').addEventListener('click', () => {
        const onu = validateOnu();
        if (!onu) return;
        checkFrame.textContent = `show gpon onu state gpon-olt_${data.card}\nshow gpon onu state gpon-olt_${data.card} | include ${onu}`;
    });

    document.getElementById('btn-generate').addEventListener('click', () => {
        const onu = validateOnu();
        if (!onu) return;

        const customer = sanitizeName(data.customer_name) || `PELANGGAN-${onu}`;
        const olt = `gpon-olt_${data.card}`;
        const onuInterface = `gpon-onu_${data.card}:${onu}`;

        const first = [
            'conf t',
            `interface ${olt}`,
            `onu ${onu} type ${data.onu_type || 'ALL-ONT'} sn ${data.sn}`,
            'end'
        ].join('\n');

        const secondLines = [
            'conf t',
            `interface ${onuInterface}`,
            `name ${customer}`,
            `tcont 1 profile ${data.tcont_profile || 'server'}`,
            'gemport 1 tcont 1',
            data.vlan ? `service-port 1 vport 1 user-vlan ${data.vlan} vlan ${data.vlan}` : '',
            'exit',
            `pon-onu-mng ${onuInterface}`,
            data.vlan_profile
                ? `wan-ip 1 mode pppoe username ${data.username} password ${data.password} vlan-profile ${data.vlan_profile} host 1`
                : `wan-ip 1 mode pppoe username ${data.username} password ${data.password} host 1`,
            data.security_mgmt ? `security-mgmt ${data.security_mgmt} state enable mode forward protocol web` : '',
            'end'
        ].filter(Boolean);

        frame1.textContent = first;
        frame2.textContent = secondLines.join('\n');
        scriptInput.value = `### FRAME 1 ###\n${first}\n\n### FRAME 2 ###\n${secondLines.join('\n')}`;
    });

    document.querySelectorAll('[data-copy]').forEach((button) => {
        button.addEventListener('click', async () => {
            const target = document.getElementById(button.dataset.copy);
            try {
                await navigator.clipboard.writeText(target.textContent);
                const oldText = button.textContent;
                button.textContent = 'Tersalin';
                setTimeout(() => button.textContent = oldText, 1200);
            } catch {
                alert('Gagal menyalin. Silakan blok teks dan salin manual.');
            }
        });
    });

    document.getElementById('activation-form').addEventListener('submit', (event) => {
        if (!validateOnu()) {
            event.preventDefault();
            return;
        }
        if (!scriptInput.value.trim()) {
            event.preventDefault();
            alert('Klik Generate terlebih dahulu sebelum menyimpan.');
        }
    });
})();
</script>
</body>
</html>
