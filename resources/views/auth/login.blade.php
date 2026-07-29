<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | FN-WMS</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-br from-sky-400 via-blue-500 to-blue-700 overflow-hidden">

<div class="absolute inset-0">
    <div class="absolute -top-24 -left-20 w-96 h-96 rounded-full bg-white/10 blur-3xl"></div>
    <div class="absolute top-40 -right-20 w-80 h-80 rounded-full bg-white/10 blur-3xl"></div>
    <div class="absolute bottom-0 left-40 w-96 h-96 rounded-full bg-white/10 blur-3xl"></div>
</div>

<div class="relative min-h-screen flex items-center justify-center px-6">
    <div class="w-full max-w-md">
        <div class="rounded-[35px] bg-white/20 backdrop-blur-xl shadow-2xl border border-white/20 p-10">
            {{-- Logo dan Judul --}}
            <div class="text-center">
                <div class="flex justify-center">
                    <img
                        src="{{ asset('images/logo-login.png') }}"
                        alt="Fahasa Net"
                        {{-- PERUBAHAN: Ukuran logo diperkecil dari h-48 menjadi h-28 --}}
                        class="h-20 w-auto">
                </div>
                {{-- PERUBAHAN: Menghapus mt-3 dan menambahkan negative margin mt-[-10px] untuk merapatkan --}}
                <h1 class="text-3xl font-extrabold text-blue-900 mt-2">
                    FN-WMS
                </h1>
            </div>

            {{-- Form --}}
            {{-- PERUBAHAN: Sedikit mengurangi margin top dari mt-10 menjadi mt-8 agar seimbang --}}
            <form
                method="POST"
                action="{{ route('login') }}"
                class="mt-8 space-y-5">
                @csrf

                <div>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Email"
                        required
                        autofocus
                        class="w-full rounded-2xl border-0 bg-white px-5 py-4 shadow-lg focus:ring-2 focus:ring-blue-500">
                    @error('email')
                    <p class="text-red-200 mt-2 text-sm">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div>
                    <input
                        type="password"
                        name="password"
                        placeholder="Password"
                        required
                        class="w-full rounded-2xl border-0 bg-white px-5 py-4 shadow-lg focus:ring-2 focus:ring-blue-500">
                    @error('password')
                    <p class="text-red-200 mt-2 text-sm">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 text-white">
                        <input
                            type="checkbox"
                            name="remember"
                            class="rounded">
                        Remember Me
                    </label>
                    @if(Route::has('password.request'))
                    <a
                        href="{{ route('password.request') }}"
                        class="text-white hover:underline">
                        Lupa Password?
                    </a>
                    @endif
                </div>

                <button
                    type="submit"
                    class="w-full rounded-2xl bg-blue-900 py-4 text-lg font-semibold text-white transition hover:bg-blue-950">
                    Login
                </button>
            </form>

            <div class="mt-8 text-center text-sm text-blue-100">
                v0.1
            </div>
        </div>
    </div>
</div>

</body>
</html>