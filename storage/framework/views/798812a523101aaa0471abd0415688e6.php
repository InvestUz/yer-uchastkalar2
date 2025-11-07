<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Ер Участкалари Маълумотлар Тизими'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: linear-gradient(to bottom, #f1f5f9, #e2e8f0);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #64748b, #475569);
            border-radius: 5px;
            border: 2px solid #f1f5f9;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, #475569, #334155);
        }

        /* Smooth Scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Table Hover Effects */
        .table-hover tbody tr:hover {
            background-color: #eff6ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        /* Premium Card Shadow */
        .card-shadow {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        /* Enhanced Header Blur */
        .header-blur {
            backdrop-filter: blur(12px) saturate(180%);
            background-color: rgba(255, 255, 255, 0.92);
        }

        /* Focus States */
        a:focus,
        button:focus {
            outline: 3px solid #3b82f6;
            outline-offset: 3px;
            border-radius: 8px;
        }

        /* Navigation Active Glow */
        .nav-active {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.4);
        }

        /* Smooth Transitions */
        * {
            transition: background-color 0.2s ease, color 0.2s ease, transform 0.2s ease;
        }

        /* Back to Top Button Animation */
        #backToTop {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        /* Print Optimization */
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white;
            }

            .header-blur {
                background: white;
                box-shadow: none;
            }
        }

        /* Logo Animation */
        .logo-pulse {
            animation: logo-pulse 3s ease-in-out infinite;
        }

        @keyframes logo-pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Navigation Hover Effect */
        .nav-link {
            position: relative;
            overflow: hidden;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 3px;
            background: linear-gradient(to right, #3b82f6, #60a5fa);
            transition: width 0.3s ease;
        }

        .nav-link:hover::before {
            width: 100%;
        }

        /* Enhanced Shadow on Scroll */
        .header-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-slate-50 min-h-screen antialiased">

    <!-- Premium Header -->
    <header class="header-blur border-b-2 border-blue-200 sticky top-0 z-50 header-shadow">
        <div class="mx-auto px-6 lg:px-12 xl:px-16">
            <div class="flex items-center justify-between h-20">
                <!-- Logo & Title -->
                <div class="flex items-center space-x-4">
                    <div class="relative group">
                        <div
                            class="w-16 h-16 rounded-xl flex items-center justify-center transform transition-all duration-300 group-hover:scale-110 group-hover:rotate-3">
                            <img src="https://toshkentinvest.uz/assets/frontend/tild6238-3031-4265-a564-343037346231/tic_logo_blue.png"
                                alt="">
                        </div>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold gradient-text tracking-tight">Ер Участкалари</h1>
                        <p class="text-sm text-slate-600 font-semibold">Тошкент Инвест компанияси</p>
                    </div>
                </div>

                <!-- Enhanced Center Navigation -->
                <nav class="hidden lg:flex items-center space-x-3">



                    <a href="<?php echo e(route('yer-sotuvlar.index')); ?>"
                        class="nav-link px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-300 <?php echo e(request()->routeIs('yer-sotuvlar.index') ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg nav-active' : 'text-slate-700 hover:bg-blue-50 hover:text-blue-700'); ?>">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>Йиғма маълумотлар</span>
                        </div>
                    </a>
                    <a href="<?php echo e(route('yer-sotuvlar.svod3')); ?>"
                        class="nav-link px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-300 <?php echo e(request()->routeIs('yer-sotuvlar.svod3') ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg nav-active' : 'text-slate-700 hover:bg-blue-50 hover:text-blue-700'); ?>">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                            <span>Бўлиб тўлаш маълуотлари</span>
                        </div>
                    </a>
                    <a href="<?php echo e(route('yer-sotuvlar.list')); ?>"
                        class="nav-link px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-300 <?php echo e(request()->routeIs('yer-sotuvlar.list') ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg nav-active' : 'text-slate-700 hover:bg-blue-50 hover:text-blue-700'); ?>">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            <span>Рўйхат</span>
                        </div>
                    </a>
                    <a href="<?php echo e(route('yer-sotuvlar.monitoring')); ?>"
                        class="nav-link px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-300 <?php echo e(request()->routeIs('yer-sotuvlar.monitoring') ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg nav-active' : 'text-slate-700 hover:bg-blue-50 hover:text-blue-700'); ?>">
                        <div class="flex items-center space-x-2">

                            <span>Инфографика ва Аналитика</span>
                        </div>
                    </a>


                </nav>

                <!-- Enhanced Right Info -->
                <div class="hidden md:block text-right">
                    <div class="flex items-center space-x-2 text-sm text-slate-800 font-semibold mb-1.5">
                        <div
                            class="w-8 h-8 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span>Аукцион савдолари маълумотлари</span>
                    </div>
                    <div class="flex items-center justify-end space-x-2 text-xs text-slate-600 font-medium">
                        <div class="flex items-center space-x-1.5 bg-blue-50 px-3 py-1 rounded-full">
                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-blue-700 font-semibold"><?php echo e(now()->format('d.m.Y')); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button class="lg:hidden p-2 rounded-lg hover:bg-blue-50 transition-colors"
                    onclick="toggleMobileMenu()">
                    <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Navigation -->
            <div id="mobileMenu" class="hidden lg:hidden pb-4 space-y-2">
                <a href="<?php echo e(route('yer-sotuvlar.index')); ?>"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg <?php echo e(request()->routeIs('yer-sotuvlar.index') ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white' : 'text-slate-700 hover:bg-blue-50'); ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="font-semibold">Статистика</span>
                </a>
                <a href="<?php echo e(route('yer-sotuvlar.svod3')); ?>"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg <?php echo e(request()->routeIs('yer-sotuvlar.svod3') ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white' : 'text-slate-700 hover:bg-blue-50'); ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                    <span class="font-semibold">Свод 3</span>
                </a>
                <a href="<?php echo e(route('yer-sotuvlar.list')); ?>"
                    class="flex items-center space-x-3 px-4 py-3 rounded-lg <?php echo e(request()->routeIs('yer-sotuvlar.list') ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white' : 'text-slate-700 hover:bg-blue-50'); ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    <span class="font-semibold">Рўйхат</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content with Animation -->
    <main class="mx-auto px-6 lg:px-12 xl:px-16 py-8 lg:py-12 animate-fadeIn">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Premium Footer -->
    <footer
        class="bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 border-t-4 border-blue-600 mt-16 no-print">
        <div class="mx-auto px-6 lg:px-12 xl:px-16 py-10">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-6 md:space-y-0">
                <!-- Left Section -->
                <div class="flex items-center space-x-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-base font-bold text-white">© <?php echo e(date('Y')); ?> Тошкент шаҳар ҳокимлиги</p>
                        <p class="text-sm text-blue-200 font-medium">Барча ҳуқуқлар ҳимояланган</p>
                    </div>
                </div>

                <!-- Right Section - Contact Info -->
                <div class="flex flex-col sm:flex-row items-center space-y-3 sm:space-y-0 sm:space-x-6">
                    <a href="tel:+998712100261"
                        class="flex items-center space-x-2 text-blue-100 hover:text-white transition-all duration-300 group">
                        <div
                            class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center group-hover:bg-blue-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <span class="font-semibold">+998 (71) 210-02-61</span>
                    </a>
                    <a href="mailto:info@tashkentinvest.com"
                        class="flex items-center space-x-2 text-blue-100 hover:text-white transition-all duration-300 group">
                        <div
                            class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center group-hover:bg-blue-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="font-semibold">info@tashkentinvest.com</span>
                    </a>
                </div>
            </div>

            <!-- Bottom Section -->
            <div class="mt-8 pt-6 border-t border-slate-600">
                <div class="flex flex-col md:flex-row justify-between items-center space-y-2 md:space-y-0">
                    <p class="text-sm text-blue-200 font-medium">
                        Маълумотлар тизими - Ер участкаларини бошқариш ва мониторинг қилиш учун
                    </p>
                    <div class="flex items-center space-x-4">
                        <a href="#" class="text-blue-200 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                        <a href="#" class="text-blue-200 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                            </svg>
                        </a>
                        <a href="#" class="text-blue-200 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Enhanced Back to Top Button -->
    <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
        class="fixed bottom-8 right-8 w-14 h-14 bg-gradient-to-br from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-full shadow-2xl flex items-center justify-center transition-all duration-300 opacity-0 hover:scale-110 focus:scale-110 no-print group"
        id="backToTop">
        <svg class="w-6 h-6 transform group-hover:-translate-y-1 transition-transform" fill="none"
            stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>

    <script>
        // Back to Top Button Visibility
        window.addEventListener('scroll', function() {
            const backToTop = document.getElementById('backToTop');
            if (window.scrollY > 300) {
                backToTop.style.opacity = '1';
            } else {
                backToTop.style.opacity = '0';
            }
        });

        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }

        // Add shadow to header on scroll
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            if (window.scrollY > 10) {
                header.classList.add('header-shadow');
            } else {
                header.classList.remove('header-shadow');
            }
        });

        // Fade in animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            .animate-fadeIn {
                animation: fadeIn 0.6s ease-out;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>

</html>
<?php /**PATH C:\Users\inves\OneDrive\Ishchi stol\yer-uchastkalar\resources\views/layouts/app.blade.php ENDPATH**/ ?>