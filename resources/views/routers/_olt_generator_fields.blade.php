<div class="md:col-span-2 rounded-xl border border-blue-200 bg-blue-50 p-5">
    <h3 class="font-bold text-blue-900">Parameter Generate OLT</h3>
    <p class="mt-1 text-sm text-blue-700">
        Nilai di bawah digunakan langsung oleh Aktivasi NOC. Jangan menuliskan parameter di kode generator.
    </p>

    <div class="mt-4 grid gap-4 md:grid-cols-2">
        <div class="md:col-span-2">
            <label class="mb-2 block font-semibold">Perintah Service</label>
            <input name="service_command"
                   value="{{ old('service_command', isset($router) ? $router->service_command : 'service 1 gemport 1 cos 0 vlan {vlan}') }}"
                   class="w-full rounded-lg border-gray-300 px-4 py-3"
                   placeholder="service 1 gemport 1 cos 0 vlan {vlan}">
            <p class="mt-1 text-xs text-gray-500">Gunakan {vlan} sebagai variabel VLAN Router NAS.</p>
        </div>

        <div>
            <label class="mb-2 block font-semibold">WAN ETHUNI</label>
            <input name="wan_ethuni"
                   value="{{ old('wan_ethuni', isset($router) ? $router->wan_ethuni : '1,2,3,4') }}"
                   class="w-full rounded-lg border-gray-300 px-4 py-3">
        </div>

        <div>
            <label class="mb-2 block font-semibold">WAN SSID</label>
            <input name="wan_ssid"
                   value="{{ old('wan_ssid', isset($router) ? $router->wan_ssid : '1') }}"
                   class="w-full rounded-lg border-gray-300 px-4 py-3">
        </div>

        <div>
            <label class="mb-2 block font-semibold">WAN Service</label>
            <input name="wan_service"
                   value="{{ old('wan_service', isset($router) ? $router->wan_service : 'internet') }}"
                   class="w-full rounded-lg border-gray-300 px-4 py-3">
        </div>
    </div>
</div>
