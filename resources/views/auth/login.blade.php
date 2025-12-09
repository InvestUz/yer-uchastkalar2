<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Кириш - Yer.toshkentinvest.uz</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="bg-white rounded-2xl shadow-2xl p-6 mb-6">
                <h1 class="text-2xl md:text-3xl font-bold text-blue-900 mb-2">
                    yer.toshkentinvest.uz
                </h1>
                <p class="text-base md:text-lg text-slate-600 font-semibold">
                    тизимидан фойдаланишга рухсат бериладиганлар рўйхати
                </p>
            </div>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-2xl font-bold text-center text-slate-800 mb-8">
                Тизимга кириш
            </h2>

            <form method="POST" action="{{ route('login.post') }}" id="loginForm">
                @csrf

                <!-- Email Field -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-bold text-slate-700 mb-2">
                        Электрон почта
                    </label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        class="w-full px-4 py-3 border-2 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all @error('email') border-red-500 @else border-slate-300 @enderror"
                        placeholder="example@mail.uz"
                        required
                        autofocus
                    >
                    @error('email')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-bold text-slate-700 mb-2">
                        Парол
                    </label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="w-full px-4 py-3 border-2 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all @error('password') border-red-500 @else border-slate-300 @enderror"
                        placeholder="••••••••"
                        required
                    >
                    @error('password')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- CAPTCHA Field -->
                <div class="mb-6">
                    <label for="captcha" class="block text-sm font-bold text-slate-700 mb-2">
                        Қуйидаги кодни киритинг
                    </label>
                    <div class="flex gap-3 items-start">
                        <div class="flex-1">
                            <div class="mb-3 bg-slate-100 rounded-lg p-2 border-2 border-slate-300 inline-block">
                                <img
                                    src="{{ route('captcha.image') }}"
                                    alt="CAPTCHA"
                                    id="captcha-image"
                                    class="h-12"
                                >
                            </div>
                            <button
                                type="button"
                                onclick="refreshCaptcha()"
                                class="ml-2 text-blue-600 hover:text-blue-800 text-sm font-semibold"
                            >
                                🔄 Янгилаш
                            </button>
                            <input
                                type="text"
                                name="captcha"
                                id="captcha"
                                class="w-full px-4 py-3 border-2 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all @error('captcha') border-red-500 @else border-slate-300 @enderror"
                                placeholder="CAPTCHA кодини киритинг"
                                required
                                autocomplete="off"
                            >
                        </div>
                    </div>
                    @error('captcha')
                        <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            name="remember"
                            class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500"
                        >
                        <span class="ml-2 text-sm text-slate-700">Мени эслаб қолинг</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                >
                    Кириш
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-slate-600 text-sm">
            <p>© {{ date('Y') }} Тошкент Инвест </p>
        </div>
    </div>

    <script>
        function refreshCaptcha() {
            fetch('{{ route('captcha.refresh') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('captcha-image').src = '{{ route('captcha.image') }}?' + Date.now();
                    document.getElementById('captcha').value = '';
                }
            });
        }
    </script>
</body>
</html>
