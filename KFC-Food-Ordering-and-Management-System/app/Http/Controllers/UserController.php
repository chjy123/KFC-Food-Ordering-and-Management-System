<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Services\Auth\UserServiceFactory;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    /* ---------- Registration ---------- */
    public function showRegister()
    {
        // resources/views/User/registration.blade.php
        return view('User.registration');
    }

    public function register(Request $request, UserServiceFactory $factory)
    {
        // Normalize inputs a bit
        $request->merge([
            'email' => strtolower(trim($request->input('email', ''))),
            'name'  => trim($request->input('name', '')),
            'phoneNo' => trim((string) $request->input('phoneNo', '')),
        ]);

        $validated = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','max:255','unique:users,email'],
            'password' => ['required','confirmed','min:8'],
            'phoneNo'  => ['nullable','string','max:30'],
            'role'     => ['nullable','in:admin,customer'],
        ]);

        // 🔒 Block public admin signups
        $validated['role'] = 'customer';

        $svc  = $factory->forRole($validated['role']);
        $user = $svc->register($validated);

        auth()->login($user);

        // 🔑 Regenerate + bind session
        $request->session()->regenerate();
        session(['ip_address' => $request->ip()]);
        session(['user_agent' => substr($request->userAgent(), 0, 120)]);

        return $user->isAdmin()
            ? redirect()->route('admin.page')->with('status', 'Admin account created. Welcome!')
            : redirect()->route('home')->with('status', 'Registration successful. Welcome!');
    }

    /* ---------- Login / Logout ---------- */
    public function showLogin()
    {
        // resources/views/User/signin.blade.php
        return view('User.signin');
    }

    public function login(Request $request, UserServiceFactory $factory)
    {
        // Normalize email
        $request->merge([
            'email' => strtolower(trim($request->input('email', ''))),
        ]);

        $request->validate([
            'email'    => ['required','email'],
            'password' => ['required'],
            'remember' => ['sometimes','boolean'],
        ]);

        // Look up user
        $role = optional(User::where('email', $request->email)->first())->role ?? 'customer';
        $svc  = $factory->forRole($role);

        // Service will handle Auth::attempt and session regeneration
        $svc->login($request);

        // 🔑 Bind session to IP + UA
        session(['ip_address' => $request->ip()]);
        session(['user_agent' => substr($request->userAgent(), 0, 120)]);

        return auth()->user()->isAdmin()
            ? redirect()->route('admin.page')->with('status', 'Welcome back, admin!')
            : redirect()->route('home')->with('status', 'Signed in successfully!');
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
        $localPayments = Payment::where('user_id', auth()->id())
            ->latest('id')
            ->limit(10)
            ->get([
                'id as payment_id',
                'payment_method',
                'payment_status',
                'payment_date',
                'amount',
            ]);

        return view('User.dashboard', [
            'payments' => $localPayments,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'phoneNo' => ['nullable', 'string', 'max:30'],
            'email'   => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
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
            return back()->withErrors(['current_password' => 'Current password is incorrect.'], 'updatePassword');
        }

        $user->password = $request->input('password');
        $user->save();

        return back()->with('password_status', 'Password updated successfully.');
    }

    private function fetchPaymentsFromService($userId): array
    {
        $resp = Http::acceptJson()->get("http://127.0.0.1:8001/api/v1/payments/user/{$userId}");

        if ($resp->failed()) {
            return [];
        }

        return $resp->json('data', []);
    }
}
