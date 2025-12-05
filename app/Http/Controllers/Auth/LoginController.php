<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('yer-sotuvlar.monitoring');
        }

        // Generate CAPTCHA
        $this->generateCaptcha();

        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'captcha' => 'required',
        ], [
            'email.required' => 'Электрон почта киритилиши шарт',
            'email.email' => 'Электрон почта формати нотўғри',
            'password.required' => 'Парол киритилиши шарт',
            'captcha.required' => 'CAPTCHA кодини киритинг',
        ]);

        // Verify CAPTCHA
        if (!$this->verifyCaptcha($request->captcha)) {
            $this->generateCaptcha(); // Regenerate on failure
            return back()->withErrors([
                'captcha' => 'CAPTCHA коди нотўғри. Қайта уриниб кўринг.'
            ])->withInput($request->except('password', 'captcha'));
        }

        // Attempt login
        $credentials = $request->only('email', 'password');
        $credentials['is_active'] = true; // Only active users

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            Session::forget('captcha_code'); // Clear CAPTCHA

            return redirect()->intended(route('yer-sotuvlar.monitoring'));
        }

        // Failed login
        $this->generateCaptcha(); // Regenerate on failure
        return back()->withErrors([
            'email' => 'Электрон почта ёки парол нотўғри.',
        ])->withInput($request->except('password', 'captcha'));
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Generate CAPTCHA code
     */
    private function generateCaptcha()
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code = '';
        for ($i = 0; $i < 6; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        Session::put('captcha_code', $code);
    }

    /**
     * Verify CAPTCHA code
     */
    private function verifyCaptcha($input)
    {
        $stored = Session::get('captcha_code');
        return $stored && strtoupper($input) === $stored;
    }

    /**
     * Generate CAPTCHA image
     */
    public function captcha()
    {
        $code = Session::get('captcha_code', 'ERROR');

        // Create image
        $width = 150;
        $height = 50;
        $image = imagecreate($width, $height);

        // Colors
        $bgColor = imagecolorallocate($image, 240, 240, 240);
        $textColor = imagecolorallocate($image, 0, 0, 100);
        $lineColor = imagecolorallocate($image, 200, 200, 200);

        // Add noise lines
        for ($i = 0; $i < 5; $i++) {
            imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $lineColor);
        }

        // Add text with angle and spacing
        $fontSize = 20;
        $x = 10;
        for ($i = 0; $i < strlen($code); $i++) {
            $angle = rand(-15, 15);
            $y = rand(30, 40);
            imagestring($image, 5, $x, $y - 15, $code[$i], $textColor);
            $x += 22;
        }

        // Output image
        header('Content-Type: image/png');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        imagepng($image);
        imagedestroy($image);
        exit;
    }

    /**
     * Refresh CAPTCHA
     */
    public function refreshCaptcha()
    {
        $this->generateCaptcha();
        return response()->json(['success' => true]);
    }
}
