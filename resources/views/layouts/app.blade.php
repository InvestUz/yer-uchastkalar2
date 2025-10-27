<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Yer Участкалари Маълумотлар Тизими')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
        }
        .table-hover tbody tr:hover {
            background-color: #f3f4f6;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-600 rounded flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-semibold text-gray-900">Ер Участкалари</h1>
                        <p class="text-xs text-gray-500">Тошкент шаҳри</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Аукцион савдолари маълумотлари</p>
                    <p class="text-xs text-gray-400">{{ now()->format('d.m.Y H:i') }}</p>
                </div>
            </div>
        </div>
    </header>

<main class="w-full max-w-screen mx-auto px-4 sm:px-6 lg:px-10 xl:px-16 py-8 md:py-10">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-gray-600">© {{ date('Y') }} Тошкент шаҳар ҳокимлиги</p>
                <p class="text-xs text-gray-400 mt-2 md:mt-0">Барча ҳуқуқлар ҳимояланган</p>
            </div>
        </div>
    </footer>
</body>
</html>
