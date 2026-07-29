<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FN-WMS | Fahasa Net</title>

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="bg-slate-100 min-h-screen flex items-center justify-center">

<div class="bg-white rounded-2xl shadow-2xl p-10 w-full max-w-lg text-center">

    <h1 class="text-5xl font-bold text-blue-700">
        FN-WMS
    </h1>

    <p class="mt-4 text-gray-600">
        Fahasa Net Warehouse Management System
    </p>

    <div class="mt-8">
        <a href="{{ route('login') }}"
           class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
            Login
        </a>
    </div>

    <p class="mt-10 text-gray-400 text-sm">
        Version 1.0
    </p>

</div>

</body>
</html>