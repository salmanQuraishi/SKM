<?php

namespace App\Http\Controllers;

use App\Mail\EmailOtpMail;
use App\Models\EmailOtp;
use App\Models\Role;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CustomerAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->intended(route('account.dashboard'));
        }
        return view('frontend.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $guestSessionId = session()->getId();

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                if ($request->ajax() || $request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Your account has been deactivated. Please contact support.'], 403);
                }
                return redirect()->route('login')->with('error', 'Your account has been deactivated. Please contact support.');
            }

            // Merge guest cart items into authenticated user's cart
            try {
                app(CartService::class)->mergeGuestCart($user->id, $guestSessionId);
            } catch (\Throwable $e) {
                Log::warning('Cart merge error on login: ' . $e->getMessage());
            }

            $request->session()->regenerate();

            $redirectUrl = session()->pull('url.intended', route('account.dashboard'));

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect' => $redirectUrl,
                    'message' => 'Welcome back, ' . $user->name . '!',
                ]);
            }

            return redirect()->to($redirectUrl)->with('success', 'Welcome back, ' . $user->name . '!');
        }

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials do not match our records.',
            ], 422);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Send OTP for Login via Gmail (Passwordless Sign-in)
     */
    public function sendLoginOtp(Request $request)
    {
        $input = trim($request->input('email', $request->input('login_identifier', '')));
        $cleanPhone = preg_replace('/[^0-9]/', '', $input);
        if (strlen($cleanPhone) === 12 && str_starts_with($cleanPhone, '91')) {
            $cleanPhone = substr($cleanPhone, 2);
        }

        $user = null;
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', strtolower($input))->first();
        } elseif (strlen($cleanPhone) === 10) {
            $user = User::where('phone', $cleanPhone)->first();
        } else {
            $user = User::where('email', strtolower($input))->orWhere('phone', $cleanPhone)->first();
        }

        if (!$user) {
            $msg = 'No account found with this Gmail address or mobile number. Please check your input or create an account.';
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 404);
            }
            return back()->withErrors(['email' => $msg])->onlyInput('email');
        }

        if (!$user->is_active) {
            $msg = 'Your account has been deactivated. Please contact support.';
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return back()->withErrors(['email' => $msg]);
        }

        $otp = EmailOtp::createOtp($user->email, 'login', ['user_id' => $user->id], 10);

        try {
            Mail::to($user->email)->send(new EmailOtpMail($otp->otp, 'login', $user->name, 10));
        } catch (\Throwable $e) {
            Log::error("Failed to send login OTP email to {$user->email}: " . $e->getMessage());
        }

        Log::info("Login OTP generated for {$user->email}: {$otp->otp}");

        // Mask email for privacy
        $parts = explode('@', $user->email);
        $maskedName = substr($parts[0], 0, 2) . str_repeat('*', max(3, strlen($parts[0]) - 2));
        $maskedEmail = $maskedName . '@' . ($parts[1] ?? 'gmail.com');

        $msg = "A 6-digit login OTP has been sent to your registered Gmail ({$maskedEmail}).";

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'email' => $user->email,
                'masked_email' => $maskedEmail,
            ]);
        }

        return back()->with([
            'otp_sent' => true,
            'otp_email' => $user->email,
            'login_mode' => 'otp',
            'success' => $msg,
        ]);
    }

    /**
     * Verify OTP and Login via Gmail
     */
    public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $email = strtolower(trim($request->email));
        $record = EmailOtp::verifyOtp($email, $request->otp, 'login');

        if (!$record) {
            $msg = 'Invalid or expired OTP. Please enter the correct 6-digit code or request a new one.';
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->withErrors(['otp' => $msg])->with([
                'otp_sent' => true,
                'otp_email' => $email,
                'login_mode' => 'otp',
            ]);
        }

        $user = User::where('email', $email)->first();
        if (!$user || !$user->is_active) {
            $msg = 'User account is inactive or not found.';
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return redirect()->route('login')->with('error', $msg);
        }

        if ($user->email_verified_at === null) {
            $user->update(['email_verified_at' => now()]);
        }

        $guestSessionId = session()->getId();
        Auth::login($user, true);

        // Merge guest cart items into authenticated user's cart
        try {
            app(CartService::class)->mergeGuestCart($user->id, $guestSessionId);
        } catch (\Throwable $e) {
            Log::warning('Cart merge error on login OTP: ' . $e->getMessage());
        }

        $request->session()->regenerate();

        $redirectUrl = session()->pull('url.intended', route('account.dashboard'));

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => $redirectUrl,
                'message' => 'Logged in successfully via Gmail OTP!',
            ]);
        }

        return redirect()->to($redirectUrl)->with('success', 'Logged in successfully via Gmail OTP!');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->intended(route('account.dashboard'));
        }
        return view('frontend.auth.register');
    }

    /**
     * Step 1 of Registration: Validate details and send Gmail OTP
     */
    public function sendRegisterOtp(Request $request)
    {
        // Sanitize mobile number (remove spaces, hyphens, country code prefixes)
        if ($request->filled('phone')) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $request->phone);
            if (strlen($cleanPhone) === 12 && str_starts_with($cleanPhone, '91')) {
                $cleanPhone = substr($cleanPhone, 2);
            }
            $request->merge(['phone' => $cleanPhone]);
        }

        $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|string|email|max:150|unique:users,email',
            'phone' => 'required|string|regex:/^[0-9]{10}$/|unique:users,phone',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.required' => 'Gmail / Email address is required.',
            'email.email' => 'Please enter a valid Gmail / Email address.',
            'email.unique' => 'This Gmail / Email is already registered. Please login to your account or use another email.',
            'phone.required' => '10-digit mobile number is required.',
            'phone.regex' => 'Mobile number must be a valid 10-digit number (e.g. 9876543210).',
            'phone.unique' => 'This mobile number is already registered with another account. Please use another number or login.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        $email = strtolower(trim($request->email));
        $name = trim($request->name);
        $phone = trim($request->phone);

        $payload = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make($request->password),
        ];

        $otp = EmailOtp::createOtp($email, 'register', $payload, 10);

        try {
            Mail::to($email)->send(new EmailOtpMail($otp->otp, 'register', $name, 10));
        } catch (\Throwable $e) {
            Log::error("Failed to send registration OTP email to {$email}: " . $e->getMessage());
        }

        Log::info("Registration OTP generated for {$email}: {$otp->otp}");

        session()->put('pending_register_email', $email);

        $msg = "A 6-digit verification code has been sent to {$email}. Please enter it below to complete registration.";

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'email' => $email,
            ]);
        }

        return back()->with([
            'otp_sent' => true,
            'otp_email' => $email,
            'success' => $msg,
        ]);
    }

    /**
     * Step 2 of Registration: Verify OTP and create user
     */
    public function verifyRegisterOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $email = strtolower(trim($request->email));
        $record = EmailOtp::verifyOtp($email, $request->otp, 'register');

        if (!$record) {
            $msg = 'Invalid or expired OTP. Please verify the code sent to your Gmail or request a new one.';
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->withErrors(['otp' => $msg])->with([
                'otp_sent' => true,
                'otp_email' => $email,
            ]);
        }

        $payload = $record->payload ?? [];
        $phone = $payload['phone'] ?? null;

        // Double-check duplicate Gmail check before creating record
        if (User::where('email', $email)->exists()) {
            $msg = 'This Gmail / Email is already registered. Please login to your account.';
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->route('login')->with('warning', $msg);
        }

        // Double-check duplicate Mobile Number check before creating record
        if ($phone && User::where('phone', $phone)->exists()) {
            $msg = 'This mobile number is already registered with another account. Please use a different mobile number or login.';
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->route('register')->with('error', $msg);
        }

        $customerRole = Role::whereIn('slug', ['user', 'customer'])->first();
        $user = User::create([
            'name' => $payload['name'] ?? 'Valued Customer',
            'email' => $email,
            'phone' => $phone,
            'password' => $payload['password'] ?? Hash::make(Str::random(12)),
            'role_id' => $customerRole ? $customerRole->id : null,
            'email_verified_at' => now(),
            'is_active' => true,
        ]);

        $guestSessionId = session()->getId();
        Auth::login($user, true);

        // Merge guest cart items into authenticated user's cart
        try {
            app(CartService::class)->mergeGuestCart($user->id, $guestSessionId);
        } catch (\Throwable $e) {
            Log::warning('Cart merge error on registration: ' . $e->getMessage());
        }

        $request->session()->regenerate();
        session()->forget('pending_register_email');

        // Determine destination: if intended URL exists or cart has items, go to checkout
        $redirectUrl = session()->pull('url.intended');
        if (!$redirectUrl) {
            try {
                $cart = app(CartService::class)->getCart();
                if ($cart->items->isNotEmpty()) {
                    $redirectUrl = route('checkout');
                }
            } catch (\Throwable $e) {
                // Ignore
            }
        }

        if (!$redirectUrl) {
            $redirectUrl = route('account.dashboard');
        }

        $successMsg = 'Registration & Gmail verification successful! Welcome to ' . \App\Models\Setting::get('store_name', 'SKM Saller') . '.';

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => $redirectUrl,
                'message' => $successMsg,
            ]);
        }

        return redirect()->to($redirectUrl)->with('success', $successMsg);
    }

    /**
     * Resend Register OTP
     */
    public function resendRegisterOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = strtolower(trim($request->email));
        $lastOtp = EmailOtp::where('email', $email)->where('action', 'register')->latest('id')->first();

        if (!$lastOtp) {
            $msg = 'No pending registration found for this email. Please fill out the registration form again.';
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 404);
            }
            return redirect()->route('register')->with('error', $msg);
        }

        // 30 seconds rate-limit check
        if ($lastOtp->created_at->diffInSeconds(now()) < 30) {
            $remaining = 30 - $lastOtp->created_at->diffInSeconds(now());
            $msg = "Please wait {$remaining} seconds before requesting another OTP.";
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 429);
            }
            return back()->withErrors(['otp' => $msg])->with(['otp_sent' => true, 'otp_email' => $email]);
        }

        $payload = $lastOtp->payload;
        $name = $payload['name'] ?? 'Valued Customer';
        $otp = EmailOtp::createOtp($email, 'register', $payload, 10);

        try {
            Mail::to($email)->send(new EmailOtpMail($otp->otp, 'register', $name, 10));
        } catch (\Throwable $e) {
            Log::error("Failed to resend register OTP email to {$email}: " . $e->getMessage());
        }

        Log::info("Resent register OTP for {$email}: {$otp->otp}");

        $msg = "A fresh 6-digit OTP has been sent to {$email}.";

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        }

        return back()->with([
            'otp_sent' => true,
            'otp_email' => $email,
            'success' => $msg,
        ]);
    }

    /**
     * Fallback standard register route
     */
    public function register(Request $request)
    {
        return $this->sendRegisterOtp($request);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been logged out successfully.');
    }
}
