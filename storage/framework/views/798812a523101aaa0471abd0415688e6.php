<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Ер Участкалари Маълумотлар Тизими'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Professional Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }

        /* Print Optimization */
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white;
            }

            .sidebar {
                display: none !important;
            }

            main {
                margin-left: 0 !important;
            }
        }

        /* Sidebar Styles */
        .sidebar {
            transition: all 0.3s ease;
            width: 280px;
        }

        /* Desktop Collapsed State */
        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar.collapsed .nav-text {
            display: none;
        }

        .sidebar.collapsed .logo-full {
            display: none;
        }

        .sidebar.collapsed .logo-mini {
            display: block !important;
        }

        .logo-mini {
            display: none;
        }

        /* Mobile Sidebar - Hidden by default */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }
        }

        /* Mobile Overlay */
        .mobile-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 40;
        }

        .mobile-overlay.active {
            display: block;
        }

        /* Navigation Active State */
        .nav-item.active {
            background-color: #2563eb;
            color: white;
            font-weight: 600;
        }

        .nav-item.active svg {
            color: white;
        }

        .nav-item.active .nav-text {
            color: white;
        }

        /* Navigation Hover State */
        .nav-item:hover:not(.active) {
            background-color: #f1f5f9;
        }

        /* Content Wrapper */
        .content-wrapper {
            margin-left: 280px;
            transition: margin-left 0.3s ease;
        }

        .content-wrapper.sidebar-collapsed {
            margin-left: 80px;
        }

        @media (max-width: 1024px) {
            .content-wrapper {
                margin-left: 0 !important;
            }
        }
    </style>
</head>

<body class="bg-gray-50 antialiased">

    <!-- Mobile Overlay -->
    <div class="mobile-overlay no-print" id="mobileOverlay" onclick="closeMobileSidebar()"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar fixed top-0 left-0 h-full bg-white border-r border-gray-200 z-50 no-print">
        <div class="h-full flex flex-col">
            <!-- Logo Section -->
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">


                    <!-- Mini Logo (collapsed state) -->
                    <div class="logo-mini flex items-center justify-around w-full">
                        <img src="https://toshkentinvest.uz/assets/frontend/tild6238-3031-4265-a564-343037346231/tic_logo_blue.png"
                            alt="Logo" class="w-24">
                    </div>

                    <!-- Toggle Button (Desktop) -->
                    <button onclick="toggleDesktopSidebar()" class="p-2 rounded hover:bg-gray-100 hidden lg:block">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Close Button (Mobile) -->
                    <button onclick="closeMobileSidebar()" class="p-2 rounded hover:bg-gray-100 lg:hidden">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- User Info Section -->
            <?php if(auth()->guard()->check()): ?>
            <div class="p-4 border-b border-gray-200 bg-blue-50">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center">
                            <span class="text-white font-bold text-sm"><?php echo e(strtoupper(substr(auth()->user()->name, 0, 2))); ?></span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate nav-text"><?php echo e(auth()->user()->name); ?></p>
                        <p class="text-xs text-gray-600 truncate nav-text"><?php echo e(auth()->user()->email); ?></p>
                        <?php if(auth()->user()->isSuperAdmin()): ?>
                            <span class="inline-block mt-1 px-2 py-1 text-xs font-semibold text-white bg-green-600 rounded nav-text">Администратор</span>
                        <?php else: ?>
                            <span class="inline-block mt-1 px-2 py-1 text-xs font-semibold text-white bg-blue-600 rounded nav-text">Худуд</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto p-3">
                <div class="space-y-1">

                    <!-- Monitoring bo'limi -->
                    <div class="px-3 py-2 mt-4">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider nav-text">Мониторинг</p>
                    </div>

                    <a href="<?php echo e(route('yer-sotuvlar.monitoring')); ?>"
                        class="nav-item flex items-center space-x-3 px-3 py-2.5 rounded-lg <?php echo e(request()->routeIs('yer-sotuvlar.monitoring') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5 <?php echo e(request()->routeIs('yer-sotuvlar.monitoring') ? 'text-white' : 'text-gray-600'); ?>"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                        </svg>
                        <span
                            class="nav-text text-sm font-medium <?php echo e(request()->routeIs('yer-sotuvlar.monitoring') ? 'text-white' : 'text-gray-700'); ?>">Инфографика</span>
                    </a>


                    <!-- Asosiy bo'lim -->
                    <div class="px-3 py-2">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider nav-text">Асосий</p>
                    </div>

                    <a href="<?php echo e(route('yer-sotuvlar.index')); ?>"
                        class="nav-item flex items-center space-x-3 px-3 py-2.5 rounded-lg <?php echo e(request()->routeIs('yer-sotuvlar.index') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5 <?php echo e(request()->routeIs('yer-sotuvlar.index') ? 'text-white' : 'text-gray-600'); ?>"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span
                            class="nav-text text-sm font-medium <?php echo e(request()->routeIs('yer-sotuvlar.index') ? 'text-white' : 'text-gray-700'); ?>">Ер мониторинг</span>
                    </a>

                    <a href="<?php echo e(route('yer-sotuvlar.yigma')); ?>"
                        class="nav-item flex items-center space-x-3 px-3 py-2.5 rounded-lg <?php echo e(request()->routeIs('yer-sotuvlar.yigma') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5 <?php echo e(request()->routeIs('yer-sotuvlar.yigma') ? 'text-white' : 'text-gray-600'); ?>"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span
                            class="nav-text text-sm font-medium <?php echo e(request()->routeIs('yer-sotuvlar.yigma') ? 'text-white' : 'text-gray-700'); ?>">Тўлов мониторинг</span>
                    </a>
                    <a href="<?php echo e(route('yer-sotuvlar.svod3')); ?>"
                        class="nav-item flex items-center space-x-3 px-3 py-2.5 rounded-lg <?php echo e(request()->routeIs('yer-sotuvlar.svod3') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5 <?php echo e(request()->routeIs('yer-sotuvlar.svod3') ? 'text-white' : 'text-gray-600'); ?>"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                        </svg>
                        <span
                            class="nav-text text-sm font-medium <?php echo e(request()->routeIs('yer-sotuvlar.svod3') ? 'text-white' : 'text-gray-700'); ?>">Бўлиб тўлаш мониторинги</span>
                    </a>

                    <a href="<?php echo e(route('yer-sotuvlar.list')); ?>"
                        class="nav-item flex items-center space-x-3 px-3 py-2.5 rounded-lg <?php echo e(request()->routeIs('yer-sotuvlar.list') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5 <?php echo e(request()->routeIs('yer-sotuvlar.list') ? 'text-white' : 'text-gray-600'); ?>"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        <span
                            class="nav-text text-sm font-medium <?php echo e(request()->routeIs('yer-sotuvlar.list') ? 'text-white' : 'text-gray-700'); ?>">Рўйхат</span>
                    </a>



                    <!-- Sozlamalar bo'limi -->
                    <div class="px-3 py-2 mt-4">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider nav-text">Созламалар</p>
                    </div>

                    <a href="<?php echo e(route('qoldiq.index')); ?>"
                        class="nav-item flex items-center space-x-3 px-3 py-2.5 rounded-lg <?php echo e(request()->routeIs('qoldiq.index') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5 <?php echo e(request()->routeIs('qoldiq.index') ? 'text-white' : 'text-gray-600'); ?>"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span
                            class="nav-text text-sm font-medium <?php echo e(request()->routeIs('qoldiq.index') ? 'text-white' : 'text-gray-700'); ?>">Қолдиқ</span>
                    </a>


                    <!-- Full Export Button (Complete Excel Structure) -->

                    <a href="<?php echo e(route('export.full')); ?>"
                        class="nav-item flex items-center space-x-3 px-3 py-2.5 rounded-lg <?php echo e(request()->routeIs('export.full') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span
                            class="nav-text text-sm font-medium <?php echo e(request()->routeIs('export.full') ? 'text-white' : 'text-gray-700'); ?>">To'liq
                            eksport (Grafik bilan)</span>
                    </a>


                    <a href="<?php echo e(route('export.summary')); ?>"
                        class="nav-item flex items-center space-x-3 px-3 py-2.5 rounded-lg <?php echo e(request()->routeIs('export.summary') ? 'active' : ''); ?>">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span
                            class="nav-text text-sm font-medium <?php echo e(request()->routeIs('export.summary') ? 'text-white' : 'text-gray-700'); ?>">Qisqacha
                            hisobot</span>
                    </a>
                </div>



            </nav>

            <!-- Logout Button -->
            <?php if(auth()->guard()->check()): ?>
            <div class="p-3 border-t border-gray-200">
                <form method="POST" action="<?php echo e(route('logout')); ?>" id="logoutForm">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="nav-item w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg hover:bg-red-50 text-red-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="nav-text text-sm font-medium">Чиқиш</span>
                    </button>
                </form>
            </div>
            <?php endif; ?>

            <!-- Footer Info -->
            <div class="p-4 border-t border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="nav-text">
                        <p class="text-xs font-semibold text-gray-900"><?php echo e(now()->format('d.m.Y')); ?></p>
                        <p class="text-xs text-gray-500"><?php echo e(now()->format('H:i')); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Top Header -->
    <header class="content-wrapper bg-white border-b border-gray-200 sticky top-0 z-30 no-print">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Mobile Menu Button -->
                <button onclick="openMobileSidebar()" class="lg:hidden p-2 rounded hover:bg-gray-100">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Page Title -->
                <div>
                    <h1 class="text-lg font-bold text-gray-900"><?php echo $__env->yieldContent('page_title', 'Ер Участкалари'); ?></h1>
                    <p class="text-sm text-gray-500"><?php echo $__env->yieldContent('page_subtitle', 'Маълумотлар тизими'); ?></p>
                </div>

                <!-- User Info -->
                <div class="flex items-center space-x-3">
                    <div class="hidden md:block text-right">
                        <p class="text-sm font-semibold text-gray-900">Админ</p>
                        <p class="text-xs text-gray-500">Тошкент Инвест</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="content-wrapper min-h-screen">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Footer -->
    <footer class="content-wrapper bg-white border-t border-gray-200 mt-8 no-print">
        <div class="px-6 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div>
                    <p class="text-sm font-semibold text-gray-900">© <?php echo e(date('Y')); ?> Тошкент шаҳар ҳокимлиги</p>
                    <p class="text-xs text-gray-500">Барча ҳуқуқлар ҳимояланган</p>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="tel:+998712100261" class="text-sm text-gray-600 hover:text-blue-600">
                        +998 (71) 210-02-61
                    </a>
                    <a href="mailto:info@tashkentinvest.com" class="text-sm text-gray-600 hover:text-blue-600">
                        info@tashkentinvest.com
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top -->
    <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
        class="fixed bottom-6 right-6 w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-lg flex items-center justify-center opacity-0 transition-opacity no-print"
        id="backToTop">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>

    <script>
        // Desktop Sidebar Toggle
        function toggleDesktopSidebar() {
            const sidebar = document.getElementById('sidebar');
            const contentWrappers = document.querySelectorAll('.content-wrapper');

            sidebar.classList.toggle('collapsed');
            contentWrappers.forEach(wrapper => {
                wrapper.classList.toggle('sidebar-collapsed');
            });

            // Save state
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }

        // Mobile Sidebar Open
        function openMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');

            sidebar.classList.add('mobile-open');
            overlay.classList.add('active');
        }

        // Mobile Sidebar Close
        function closeMobileSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');

            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        }

        // Window Resize Handler
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                // Desktop: Remove mobile classes
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('mobileOverlay');
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
            }
        });

        // Load saved sidebar state (desktop)
        window.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth >= 1024) {
                const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (isCollapsed) {
                    const sidebar = document.getElementById('sidebar');
                    const contentWrappers = document.querySelectorAll('.content-wrapper');

                    sidebar.classList.add('collapsed');
                    contentWrappers.forEach(wrapper => {
                        wrapper.classList.add('sidebar-collapsed');
                    });
                }
            }
        });

        // Back to Top Button
        window.addEventListener('scroll', function() {
            const backToTop = document.getElementById('backToTop');
            if (window.scrollY > 300) {
                backToTop.style.opacity = '1';
            } else {
                backToTop.style.opacity = '0';
            }
        });
    </script>
</body>

</html>
<?php /**PATH C:\Users\inves\OneDrive\Ishchi stol\yer-uchastkalar\resources\views/layouts/app.blade.php ENDPATH**/ ?>