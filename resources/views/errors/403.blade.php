<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akses Ditolak</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100">
    <main class="flex min-h-screen items-center justify-center px-4">
        <section class="w-full max-w-lg rounded-3xl border border-slate-200 bg-white p-8 text-center shadow-xl">
            <div class="text-6xl">🔒</div>
            <p class="mt-5 text-sm font-bold uppercase tracking-widest text-red-600">403</p>
            <h1 class="mt-2 text-2xl font-black text-slate-900">Akses Ditolak</h1>
            <p class="mt-3 text-sm leading-6 text-slate-600">
                Menu Aktivasi hanya dapat diakses oleh Super User, Admin, dan NOC.
            </p>
            <a
                href="{{ route('dashboard') }}"
                class="mt-7 inline-flex h-12 items-center justify-center rounded-xl bg-blue-700 px-6 text-sm font-bold text-white transition hover:bg-blue-800"
            >
                Kembali ke Dashboard
            </a>
        </section>
    </main>
</body>
</html>
