<header class="h-20 bg-white border-b border-gray-200 shadow-sm">

    <div class="flex items-center justify-between h-full px-8">

        {{-- LEFT --}}
        <div class="flex items-center gap-5">

            {{-- Tombol Collapse (dipakai nanti) --}}
            <button
                class="w-10 h-10 rounded-xl hover:bg-gray-100 transition flex items-center justify-center">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-6 h-6 text-gray-600"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>

                </svg>

            </button>

            <div>

                <h1 class="text-2xl font-bold text-gray-800">

                    @yield('page-title','Dashboard')

                </h1>

            </div>

        </div>

        {{-- RIGHT --}}
        <div class="flex items-center">

            <button
                class="flex items-center gap-3 rounded-xl px-3 py-2 hover:bg-gray-100 transition">

                <div class="text-right">

                    <div class="font-semibold text-gray-800">

                        {{ Auth::user()->name }}

                    </div>

                    <div class="text-xs text-gray-500">

                        Administrator

                    </div>

                </div>

                <div
                    class="w-11 h-11 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">

                    {{ strtoupper(substr(Auth::user()->name,0,1)) }}

                </div>

            </button>

        </div>

    </div>

</header>