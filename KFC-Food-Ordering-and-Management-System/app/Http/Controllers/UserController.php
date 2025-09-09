<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Services\Auth\UserServiceFactory;

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
        // Optional: allow role in form, but we will lock it down below
        'role'     => ['nullable','in:admin,customer'],
    ]);

    // 🔒 Block public admin signups (uncomment next line to allow invite-only later)
    $validated['role'] = 'customer';

    $svc  = $factory->forRole($validated['role']);
    $user = $svc->register($validated);

    auth()->login($user);
    $request->session()->regenerate();

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

    // Look up user to route to correct role service (default to customer if not found)
    $role = optional(User::where('email', $request->email)->first())->role ?? 'customer';
    $svc  = $factory->forRole($role);

    // Service will handle Auth::attempt and session regeneration
    $svc->login($request);

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
        // Author: Pang Jun Meng
        $payments = Payment::where('user_id', Auth::id())
            ->orderByDesc('id') 
            ->limit(10)
            ->get([
                'id as payment_id',      
                'payment_method',
                'payment_status',
                'payment_date',            
                'amount',
            ]);

        return view('User.dashboard', compact('payments'));
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
            // send to named error bag "updatePassword"
            return back()->withErrors(['current_password' => 'Current password is incorrect.'], 'updatePassword');
        }

        // Model cast will hash automatically
        $user->password = $request->input('password');
        $user->save();

        return back()->with('password_status', 'Password updated successfully.');
    }
}
