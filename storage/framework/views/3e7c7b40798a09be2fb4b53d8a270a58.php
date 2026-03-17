<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
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

            <form method="POST" action="<?php echo e(route('login.post')); ?>" id="loginForm">
                <?php echo csrf_field(); ?>

                <!-- Email Field -->
                <div class="mb-6">
                    <label for="email" class="block text-sm font-bold text-slate-700 mb-2">
                        Электрон почта
                    </label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="<?php echo e(old('email')); ?>"
                        class="w-full px-4 py-3 border-2 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php else: ?> border-slate-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        placeholder="example@mail.uz"
                        required
                        autofocus
                    >
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-600 text-sm mt-2"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                        class="w-full px-4 py-3 border-2 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php else: ?> border-slate-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        placeholder="••••••••"
                        required
                    >
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-600 text-sm mt-2"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                                    src="<?php echo e(route('captcha.image')); ?>"
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
                                class="w-full px-4 py-3 border-2 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all <?php $__errorArgs = ['captcha'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php else: ?> border-slate-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                placeholder="CAPTCHA кодини киритинг"
                                required
                                autocomplete="off"
                            >
                        </div>
                    </div>
                    <?php $__errorArgs = ['captcha'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-600 text-sm mt-2"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
            <p>© <?php echo e(date('Y')); ?> Тошкент Инвест </p>
        </div>
    </div>

    <script>
        function refreshCaptcha() {
            fetch('<?php echo e(route('captcha.refresh')); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('captcha-image').src = '<?php echo e(route('captcha.image')); ?>?' + Date.now();
                    document.getElementById('captcha').value = '';
                }
            });
        }
    </script>
</body>
</html>
<?php /**PATH C:\Users\inves\OneDrive\Ishchi stol\yer-uchastkalar\resources\views/auth/login.blade.php ENDPATH**/ ?>