<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Proses Aktivasi NOC</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
<header class="bg-slate-950 text-white">
    <div class="mx-auto flex max-w-7xl items-center gap-3 px-4 py-4">
        <a href="{{ route('noc-activations.processing') }}" class="rounded-lg bg-white/10 px-4 py-3">←</a>
        <div>
            <h1 class="font-bold">Proses Aktivasi NOC</h1>
            <p class="text-xs text-slate-400">Seluruh parameter OLT berasal dari Router NAS dan Master ODP</p>
        </div>
    </div>
</header>

<main class="mx-auto max-w-7xl px-4 py-5">
    @if($errors->any())
        <div class="mb-4 rounded-xl bg-red-50 p-4 text-red-800">
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    <form id="activation-form" method="POST" action="{{ route('noc-activations.complete', $nocActivation) }}">
        @csrf

        <section class="grid gap-3 md:grid-cols-3">
            @foreach([
                ['Router NAS', $data['nas']],
                ['Pelanggan', $data['customer_name']],
                ['SN', $data['sn']],
                ['Master ODP', $data['odp']],
                ['Card OLT', $data['card']],
                ['Range ONU', $data['onu_start'].' - '.$data['onu_end']],
                ['Username', $data['username']],
                ['Password', $data['password']],
                ['ONU Type', $data['onu_type']],
                ['VLAN', $data['vlan']],
                ['VLAN Profile', $data['vlan_profile']],
                ['TCONT Profile', $data['tcont_profile']],
            ] as [$label, $value])
                <div class="rounded-xl bg-white p-4 shadow">
                    <p class="text-xs font-bold text-slate-400">{{ $label }}</p>
                    <p class="mt-1 break-all font-mono font-bold">{{ $value }}</p>
                </div>
            @endforeach
        </section>

        <section class="mt-5 rounded-xl bg-white p-5 shadow">
            <label class="mb-2 block font-bold">Nomor ONU</label>
            <div class="grid gap-3 md:grid-cols-[1fr_auto_auto]">
                <input id="onu_number" name="onu_number" type="number"
                       min="{{ $data['onu_start'] }}" max="{{ $data['onu_end'] }}"
                       value="{{ old('onu_number', $nocActivation->onu_number) }}"
                       class="h-12 rounded-lg border-slate-300" required>
                <button id="btn-check" type="button" class="rounded-lg bg-amber-500 px-5 font-bold text-white">
                    Cek ONU
                </button>
                <button id="btn-generate" type="button" class="rounded-lg bg-cyan-600 px-5 font-bold text-white">
                    Generate
                </button>
            </div>
            <p class="mt-2 text-xs text-slate-500">
                Range diambil dari Master ODP: {{ $data['onu_start'] }} sampai {{ $data['onu_end'] }}.
            </p>
        </section>

        <input type="hidden" name="odp_name" value="{{ $data['odp'] }}">
        <textarea id="provisioning_script" name="provisioning_script" class="hidden">{{ old('provisioning_script', $nocActivation->provisioning_script) }}</textarea>

        <section class="mt-5 grid gap-5 xl:grid-cols-2">
            @foreach([
                ['check-frame', 'Cek ONU Kosong'],
                ['frame-1', 'Frame 1 — Registrasi SN'],
                ['frame-2', 'Frame 2 — Konfigurasi Layanan'],
            ] as [$id, $title])
                <div class="rounded-xl bg-white p-5 shadow {{ $id === 'frame-2' ? 'xl:col-span-2' : '' }}">
                    <div class="flex justify-between">
                        <h2 class="font-bold">{{ $title }}</h2>
                        <button type="button" data-copy="{{ $id }}" class="rounded bg-slate-100 px-3 py-2 text-xs font-bold">Copy</button>
                    </div>
                    <pre id="{{ $id }}" class="mt-4 min-h-40 overflow-auto whitespace-pre-wrap rounded-xl bg-slate-950 p-4 font-mono text-sm text-emerald-300">Klik tombol di atas.</pre>
                </div>
            @endforeach
        </section>

        <section class="mt-5 rounded-xl bg-white p-5 shadow">
            <label class="mb-2 block font-bold">Catatan NOC</label>
            <textarea name="noc_notes" rows="3" class="w-full rounded-lg border-slate-300">{{ old('noc_notes', $nocActivation->noc_notes) }}</textarea>
            <div class="mt-4 flex justify-end">
                <button class="h-12 rounded-lg bg-emerald-600 px-7 font-bold text-white"
                        onclick="return confirm('Aktivasi sudah berhasil dan siap diverifikasi Admin?')">
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

    const onuNumber = () => {
        const value = Number(onuInput.value);
        const start = Number(data.onu_start);
        const end = Number(data.onu_end);

        if (!Number.isInteger(value) || value < start || value > end) {
            alert(`Nomor ONU harus berada pada range Master ODP: ${start} - ${end}.`);
            return null;
        }

        return value;
    };

    const cleanName = value => String(value || '')
        .trim()
        .replace(/\s+/g, '-')
        .replace(/[^a-zA-Z0-9._-]/g, '');

    document.getElementById('btn-check').addEventListener('click', () => {
        if (!onuNumber()) return;
        checkFrame.textContent = `show gpon onu state gpon-olt_${data.card}`;
    });

    document.getElementById('btn-generate').addEventListener('click', () => {
        const onu = onuNumber();
        if (!onu) return;

        if (!confirm('Apakah data Router NAS, Master ODP, SN dan PPPoE sudah benar?')) return;

        const customer = cleanName(data.customer_name) || `PELANGGAN-${onu}`;
        const olt = `gpon-olt_${data.card}`;
        const onuInterface = `gpon-onu_${data.card}:${onu}`;

        const serviceCommand = String(data.service_command)
            .replaceAll('{vlan}', data.vlan)
            .replaceAll('{gemport}', '1')
            .replaceAll('{service}', '1');

        const first = [
            'conf t',
            `interface ${olt}`,
            `onu ${onu} type ${data.onu_type} sn ${data.sn}`,
            '!',
            'end'
        ].join('\n');

        const second = [
            'conf t',
            `interface ${onuInterface}`,
            `name ${customer}`,
            `  tcont 1 profile ${data.tcont_profile}`,
            '  gemport 1 tcont 1',
            `  service-port 1 vport 1 user-vlan ${data.vlan} vlan ${data.vlan}`,
            '!',
            '!',
            `pon-onu-mng ${onuInterface}`,
            `  ${serviceCommand}`,
            `  wan-ip 1 mode pppoe username ${data.username} password ${data.password} vlan-profile ${data.vlan_profile} host 1`,
            `  security-mgmt ${data.security_mgmt} state enable mode forward protocol web`,
            `  wan 1 ethuni ${data.wan_ethuni} ssid ${data.wan_ssid} service ${data.wan_service} host 1`,
            '!',
            '!',
            'end'
        ].join('\n');

        frame1.textContent = first;
        frame2.textContent = second;
        scriptInput.value = `### FRAME 1 ###\n${first}\n\n### FRAME 2 ###\n${second}`;
    });

    document.querySelectorAll('[data-copy]').forEach(button => {
        button.addEventListener('click', async () => {
            await navigator.clipboard.writeText(
                document.getElementById(button.dataset.copy).textContent
            );
        });
    });

    document.getElementById('activation-form').addEventListener('submit', event => {
        if (!onuNumber() || !scriptInput.value.trim()) {
            event.preventDefault();
            alert('Validasi ONU dan klik Generate terlebih dahulu.');
        }
    });
})();
</script>
</body>
</html>
