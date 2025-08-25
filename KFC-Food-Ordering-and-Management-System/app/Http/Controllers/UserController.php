<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /* ----- Registration ----- */
    public function showRegister()
    {
        // your file is resources/views/registration.blade.php
        return view('registration');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'phoneNo'  => ['nullable', 'string', 'max:30'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => $validated['password'], // auto-hashed via casts() in your User model
            'phoneNo'  => $validated['phoneNo'] ?? null,
            'role'     => 'customer',
        ]);

        Auth::login($user);
        return redirect()->route('home')->with('status', 'Registration successful. Welcome!');
    }

    /* ----- Login ----- */
    public function showLogin()
    {
        // your file is resources/views/signin.blade.php
        return view('signin');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = (bool) $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Optional: redirect admins to dashboard
            if (Auth::user()->isAdmin()) {
                return redirect()->route('dashboard')->with('status', 'Welcome back, admin!');
            }

            return redirect()->intended(route('home'))->with('status', 'Signed in successfully!');
        }

        return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
    }

    /* ----- Logout ----- */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('status', 'You have been logged out.');
    }

    /* ----- Optional protected page ----- */
    public function dashboard()
    {
        return view('home'); // or create resources/views/dashboard.blade.php
    }
}
