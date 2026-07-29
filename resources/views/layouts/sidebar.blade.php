<aside
    x-data="{
        master: {{ request()->routeIs('customers.*', 'technicians.*', 'teams.*', 'marketings.*', 'positions.*', 'routers.*', 'odps.*', 'packages.*') ? 'true' : 'false' }},
        operasional: {{ request()->routeIs('registrations.*', 'work-orders.*', 'noc-activations.*') ? 'true' : 'false' }},
        laporan: false,
        pengaturan: {{ request()->routeIs('roles.*', 'users.*', 'permissions.*') ? 'true' : 'false' }}
    }"
    class="flex flex-col w-64 min-h-screen bg-[#0c2553] text-white shadow-xl">

    <div class="px-5 pt-6 pb-5 border-b border-white/10">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 shrink-0 flex items-center justify-center">
                <svg class="w-11 h-11 text-[#38bdf8]" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 75 L45 45" stroke="currentColor" stroke-width="10" stroke-linecap="round"/>
                    <path d="M45 45 L75 75" stroke="currentColor" stroke-width="10" stroke-linecap="round"/>
                    <path d="M55 25 L85 55" stroke="currentColor" stroke-width="10" stroke-linecap="round"/>
                    
                    <rect x="5" y="65" width="20" height="20" rx="5" fill="currentColor"/>
                    <rect x="35" y="35" width="20" height="20" rx="5" fill="currentColor"/>
                    <rect x="65" y="65" width="20" height="20" rx="5" fill="currentColor"/>
                    
                    <rect x="45" y="5" width="20" height="20" rx="5" fill="currentColor"/>
                    <rect x="75" y="45" width="20" height="20" rx="5" fill="currentColor"/>
                </svg>
            </div>
            
            <div class="flex flex-col justify-center">
                <h1 class="text-lg font-bold tracking-wider text-white leading-none">
                    FN-WMS
                </h1>
                <span class="text-[11px] text-blue-300 font-medium mt-1 leading-tight whitespace-nowrap">
                    Fahasa Net Warehouse
                </span>
            </div>
        </div>
    </div>

    <div class="px-4 py-4">
        <div class="rounded-2xl bg-white/5 border border-white/10 p-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-white text-[#0c2553] flex items-center justify-center font-bold text-lg">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                </div>
                <div>
                    <div class="font-bold text-sm text-white">
                        {{ Auth::user()->name ?? 'Administrator' }}
                    </div>
                    <div class="text-xs text-blue-300">
                        {{ Auth::user()->roleName() }}
                    </div>
                </div>
            </div>
            <button class="w-9 h-9 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-blue-200 hover:bg-white/10 transition">
                ⚙️
            </button>
        </div>
    </div>

    <div class="border-b border-white/10 mb-2"></div>

    <nav class="flex-1 px-4 py-2 overflow-y-auto text-sm space-y-1">

{{-- Dashboard --}}
@can('dashboard.view')
<a href="{{ route('dashboard') }}"
   class="flex items-center gap-3 rounded-xl px-4 py-3 transition {{ request()->routeIs('dashboard') ? 'bg-[#1e5adb] text-white shadow-lg font-semibold' : 'text-blue-200 hover:bg-white/5 font-semibold' }}">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
         viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
    </svg>
    Dashboard
</a>
@endcan

{{-- MASTER DATA --}}
@canany([
    'customers.view',
    'technicians.view',
    'teams.view',
    'marketings.view',
    'positions.view',
    'routers.view',
    'odps.view',
    'packages.view'
])

<div class="pt-1">

    <button
        @click="master = !master"
        class="flex items-center justify-between w-full px-4 py-3 rounded-xl text-blue-200 hover:bg-white/5 transition font-semibold">

        <div class="flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-5 h-5"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M3 7h18M3 12h18M3 17h18"/>
            </svg>

            <span class="tracking-wide">MASTER DATA</span>

        </div>

        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-4 h-4 transition duration-200"
             :class="{'rotate-180': master}"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor">

            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 9l-7 7-7-7"/>

        </svg>

    </button>

    <div x-show="master"
         x-transition
         class="ml-6 mt-1 space-y-0.5 border-l border-white/10 pl-4">

        @can('customers.view')
        <a href="{{ route('customers.index') }}"
           class="flex items-center gap-3 py-2 {{ request()->routeIs('customers.*') ? 'text-white font-semibold' : 'text-blue-200 hover:text-white transition' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('customers.*') ? 'bg-white' : 'bg-blue-400/50' }}"></span>
            Pelanggan
        </a>
        @endcan

        @can('technicians.view')
        <a href="{{ route('technicians.index') }}"
           class="flex items-center gap-3 py-2 {{ request()->routeIs('technicians.*') ? 'text-white font-semibold' : 'text-blue-200 hover:text-white transition' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('technicians.*') ? 'bg-white' : 'bg-blue-400/50' }}"></span>
            Teknisi
        </a>
        @endcan

        @can('teams.view')
        <a href="{{ route('teams.index') }}"
           class="flex items-center gap-3 py-2 {{ request()->routeIs('teams.*') ? 'text-white font-semibold' : 'text-blue-200 hover:text-white transition' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('teams.*') ? 'bg-white' : 'bg-blue-400/50' }}"></span>
            Tim Teknisi
        </a>
        @endcan

        @can('marketings.view')
        <a href="{{ route('marketings.index') }}"
           class="flex items-center gap-3 py-2 {{ request()->routeIs('marketings.*') ? 'text-white font-semibold' : 'text-blue-200 hover:text-white transition' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('marketings.*') ? 'bg-white' : 'bg-blue-400/50' }}"></span>
            Marketing
        </a>
        @endcan

        @can('positions.view')
        <a href="{{ route('positions.index') }}"
           class="flex items-center gap-3 py-2 {{ request()->routeIs('positions.*') ? 'text-white font-semibold' : 'text-blue-200 hover:text-white transition' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('positions.*') ? 'bg-white' : 'bg-blue-400/50' }}"></span>
            Jabatan
        </a>
        @endcan

        @can('routers.view')
        <a href="{{ route('routers.index') }}"
           class="flex items-center gap-3 py-2 {{ request()->routeIs('routers.*') ? 'text-white font-semibold' : 'text-blue-200 hover:text-white transition' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('routers.*') ? 'bg-white' : 'bg-blue-400/50' }}"></span>
            Router NAS
        </a>
        @endcan

        @can('odps.view')
        <a href="{{ route('odps.index') }}"
           class="flex items-center gap-3 py-2 {{ request()->routeIs('odps.*') ? 'text-white font-semibold' : 'text-blue-200 hover:text-white transition' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('odps.*') ? 'bg-white' : 'bg-blue-400/50' }}"></span>
            Master ODP
        </a>
        @endcan

        @can('packages.view')
        <a href="{{ route('packages.index') }}"
           class="flex items-center gap-3 py-2 {{ request()->routeIs('packages.*') ? 'text-white font-semibold' : 'text-blue-200 hover:text-white transition' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('packages.*') ? 'bg-white' : 'bg-blue-400/50' }}"></span>
            Paket Internet
        </a>
        @endcan

    </div>

</div>

@endcanany

{{-- OPERASIONAL --}}
@canany([
    'registrations.view',
    'work-orders.view',
    'noc-activations.view',
    'schedules.view',
    'surveys.view',
    'troubles.view'
])

<div>

    <button
        @click="operasional = !operasional"
        class="flex justify-between items-center w-full px-4 py-3 rounded-xl text-blue-200 hover:bg-white/5 transition font-semibold">

        <div class="flex items-center gap-3">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-5 h-5"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>

            </svg>

            <span>OPERASIONAL</span>

        </div>

        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-4 h-4 transition duration-200"
             :class="{'rotate-180': operasional}"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor">

            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 9l-7 7-7-7"/>

        </svg>

    </button>

    <div
        x-show="operasional"
        x-transition
        class="ml-6 mt-1 space-y-0.5 border-l border-white/10 pl-4">

        @can('registrations.view')
        <a href="{{ route('registrations.index') }}"
            class="flex items-center gap-3 py-2 {{ request()->routeIs('registrations.*') ? 'text-white font-semibold' : 'text-blue-200 hover:text-white transition' }}">

            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('registrations.*') ? 'bg-white' : 'bg-blue-400/50' }}"></span>

            Registrasi Pelanggan

        </a>
        @endcan

        @can('work-orders.view')
        <a href="{{ route('work-orders.index') }}"
            class="flex items-center gap-3 py-2 {{ request()->routeIs('work-orders.*') ? 'text-white font-semibold' : 'text-blue-200 hover:text-white transition' }}">

            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('work-orders.*') ? 'bg-white' : 'bg-blue-400/50' }}"></span>

            Work Order

        </a>
        @endcan
        
        {{-- AKTIVASI NOC --}}
        @can('noc-activations.view')
        <a href="{{ route('noc-activations.index') }}"
            class="flex items-center gap-3 py-2 {{ request()->routeIs('noc-activations.*') ? 'text-white font-semibold' : 'text-blue-200 hover:text-white transition' }}">

            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('noc-activations.*') ? 'bg-white' : 'bg-blue-400/50' }}"></span>

            Aktivasi NOC

            @php
                $waitingActivation = \App\Models\NocActivation::where(
                    'status',
                    \App\Models\NocActivation::STATUS_WAITING
                )->count();
            @endphp

            @if($waitingActivation > 0)
                <span
                    class="ml-auto inline-flex items-center justify-center min-w-[24px] h-6 px-2 rounded-full bg-red-500 text-white text-xs font-bold">
                    {{ $waitingActivation }}
                </span>
            @endif

        </a>
        @endcan

        @can('schedules.view')
        <a href="#"
           class="flex items-center gap-3 py-2 text-blue-200 hover:text-white transition">

            <span class="w-1.5 h-1.5 rounded-full bg-blue-400/50"></span>

            Jadwal Teknisi

        </a>
        @endcan

        @can('surveys.view')
        <a href="#"
           class="flex items-center gap-3 py-2 text-blue-200 hover:text-white transition">

            <span class="w-1.5 h-1.5 rounded-full bg-blue-400/50"></span>

            Survey

        </a>
        @endcan

        @can('troubles.view')
        <a href="#"
           class="flex items-center gap-3 py-2 text-blue-200 hover:text-white transition">

            <span class="w-1.5 h-1.5 rounded-full bg-blue-400/50"></span>

            Gangguan

        </a>
        @endcan

    </div>

</div>

@endcanany

{{-- LAPORAN --}}
@can('reports.view')

<a href="#"
   class="flex items-center justify-between w-full px-4 py-3 rounded-xl text-blue-200 hover:bg-white/5 transition font-semibold">

    <div class="flex items-center gap-3">

        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-5 h-5"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor">

            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2"/>

        </svg>

        <span>LAPORAN</span>

    </div>

    <svg xmlns="http://www.w3.org/2000/svg"
         class="w-4 h-4 text-blue-400"
         fill="none"
         viewBox="0 0 24 24"
         stroke="currentColor">

        <path stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M9 5l7 7-7 7"/>

    </svg>

</a>

@endcan

{{-- PENGATURAN --}}
@canany([
    'roles.view',
    'users.view'
])

<div>

    <button
        @click="pengaturan = !pengaturan"
        class="flex justify-between items-center w-full px-4 py-3 rounded-xl text-blue-200 hover:bg-white/5 transition font-semibold">

        <div class="flex items-center gap-3">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-5 h-5"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0"/>

            </svg>

            <span>PENGATURAN</span>

        </div>

        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-4 h-4 transition duration-200"
             :class="{'rotate-180': pengaturan}"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor">

            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 9l-7 7-7-7"/>

        </svg>

    </button>

    <div
        x-show="pengaturan"
        x-transition
        class="ml-6 mt-1 space-y-0.5 border-l border-white/10 pl-4">

        @can('roles.view')
        <a href="{{ route('roles.index') }}"
           class="flex items-center gap-3 py-2 {{ request()->routeIs('roles.*') ? 'text-white font-semibold' : 'text-blue-200 hover:text-white transition' }}">

            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('roles.*') ? 'bg-white' : 'bg-blue-400/50' }}"></span>

            Role

        </a>
        @endcan

        @can('users.view')
        <a href="{{ route('users.index') }}"
           class="flex items-center gap-3 py-2 {{ request()->routeIs('users.*') ? 'text-white font-semibold' : 'text-blue-200 hover:text-white transition' }}">

            <span class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('users.*') ? 'bg-white' : 'bg-blue-400/50' }}"></span>

            User

        </a>
        @endcan

    </div>

</div>

@endcanany

{{-- LOGOUT --}}
<div class="pt-2 border-t border-white/10">

    <form method="POST" action="{{ route('logout') }}">

        @csrf

        <button
            type="submit"
            class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-blue-200 hover:bg-white/5 transition font-semibold text-left">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-5 h-5 text-blue-300"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>

            </svg>

            <span>Logout</span>

        </button>

    </form>

</div>

</nav>

<div class="mt-auto border-t border-white/10 px-6 py-5">

    <div class="text-left">

        <div class="font-bold text-sm text-white">
            FN-WMS v0.1
        </div>

        <div class="mt-0.5 text-xs text-blue-400">
            © Fahasa Net
        </div>

    </div>

</div>

</aside>