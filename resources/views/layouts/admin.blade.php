<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>FN-WMS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""
/>

@stack('styles')
</head>

<body class="bg-gray-100">

<div class="flex h-screen">

    {{-- Sidebar --}}
    @include('layouts.sidebar')

    {{-- Content --}}
    <div class="flex flex-1 flex-col">

        {{-- Header --}}
        @include('layouts.header')

        {{-- Main Content --}}
        <main class="flex-1 overflow-y-auto p-6">

            @yield('content')

        </main>

    </div>

</div>

<script>

function openImportModal() {

    const modal = document.getElementById('importModal');

    if(modal){
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

}

function closeImportModal() {

    const modal = document.getElementById('importModal');

    if(modal){
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

}

</script>
<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin="">
</script>

@stack('scripts')
</body>

</html>