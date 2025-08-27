<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /* ---------- Registration ---------- */
    public function showRegister()
    {
        // resources/views/User/registration.blade.php
        return view('User.registration');
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

        // After register, go to customer dashboard
        return redirect()->route('dashboard')->with('status', 'Registration successful. Welcome!');
    }

    /* ---------- Login / Logout ---------- */
    public function showLogin()
    {
        // resources/views/User/signin.blade.php
        return view('User.signin');
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

            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect()->route('admin.page')->with('status', 'Welcome back, admin!');
            }
            return redirect()->route('dashboard')->with('status', 'Signed in successfully!');
        }

        return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'You have been logged out.');
    }

    /* ---------- Dashboard + Updates ---------- */
    public function dashboard()
    {
        // resources/views/User/dashboard.blade.php
        return view('User.dashboard');
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'phoneNo' => ['nullable', 'string', 'max:30'],
        ]);

        $user->fill($validated)->save();

        return back()->with('profile_status', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', 'min:8'],
        ]);

        $user = $request->user();

        if (! Hash::check($request->input('current_password'), $user->password)) {
            // send to named error bag "updatePassword"
            return back()->withErrors(['current_password' => 'Current password is incorrect.'], 'updatePassword');
        }

        // Model cast will hash automatically
        $user->password = $request->input('password');
        $user->save();

        return back()->with('password_status', 'Password updated successfully.');
    }
}
