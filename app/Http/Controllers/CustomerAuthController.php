<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('account.dashboard');
        }
        return view('frontend.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (!Auth::user()->is_active) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Your account has been deactivated. Please contact support.');
            }

            return redirect()->intended(route('account.dashboard'))->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('account.dashboard');
        }
        return view('frontend.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|string|email|max:150|unique:users',
            'phone' => 'nullable|string|regex:/^[0-9]{10}$/',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $customerRole = Role::where('slug', 'customer')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => $customerRole ? $customerRole->id : null,
            'is_active' => true,
        ]);

        Auth::login($user);

        return redirect()->route('account.dashboard')->with('success', 'Registration successful! Welcome to AutoLogos.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been logged out successfully.');
    }
}
