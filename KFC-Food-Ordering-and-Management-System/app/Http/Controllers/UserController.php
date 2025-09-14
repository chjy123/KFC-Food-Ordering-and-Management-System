<?php
// Author's Name: Chow Jun Yu
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Services\Auth\UserServiceFactory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    // Registration
    public function showRegister()
    {
        return view('User.registration');
    }

    public function register(Request $request, UserServiceFactory $factory)
    {
        // Normalize inputs
        $request->merge([
            'email'   => strtolower(trim($request->input('email', ''))),
            'name'    => trim($request->input('name', '')),
            'phoneNo' => trim((string) $request->input('phoneNo', '')),
        ]);

        $validated = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','max:255','unique:users,email'],
            'password' => ['required','confirmed','min:8'],
            'phoneNo'  => ['nullable','string','max:30'],
            'role'     => ['nullable','in:admin,customer'],
        ]);

        // Force role
        $validated['role'] = 'customer';

        $svc  = $factory->forRole($validated['role']);
        $user = $svc->register($validated);

        Auth::login($user);

        // 🔐 Secure session regeneration
        $request->session()->regenerate();
        session([
            'ip_address' => $request->ip(),
            'user_agent' => substr($request->userAgent(), 0, 120),
        ]);

        // 🔐 Create device lock secret
        $this->setDeviceLock($request);

        return $user->isAdmin()
            ? redirect()->route('admin.dashboard')->with('status', 'Admin account created. Welcome!')
            : redirect()->route('home')->with('status', 'Registration successful. Welcome!');
    }

    // Login / Logout
    public function showLogin()
    {
        return view('User.signin');
    }

    public function login(Request $request, UserServiceFactory $factory)
    {
        $request->merge([
            'email' => strtolower(trim($request->input('email', ''))),
        ]);

        $request->validate([
            'email'    => ['required','email'],
            'password' => ['required'],
            'remember' => ['sometimes','boolean'],
        ]);

        // Brute-force guard
        $key = $this->loginThrottleKey($request);
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "Too many attempts. Try again in {$seconds} seconds.",
            ])->status(429);
        }

        usleep(min(RateLimiter::attempts($key) * 200000, 1000000));

        $role = optional(User::where('email', $request->email)->first())->role ?? 'customer';
        $svc  = $factory->forRole($role);

        $ok = false;
        try {
            $ok = $svc->login($request);
        } catch (\Throwable $e) {
            $ok = false;
        }

        if (! $ok) {
            RateLimiter::hit($key, 60);
            Log::info('Login failed, attempts so far: '.RateLimiter::attempts($key));
            throw ValidationException::withMessages([
                'email' => 'Invalid credentials.',
            ]);
        }

        RateLimiter::clear($key);

        session([
            'ip_address' => $request->ip(),
            'user_agent' => substr($request->userAgent(), 0, 120),
        ]);

        // 🔐 Create device lock secret
        $this->setDeviceLock($request);

        return (Auth::user()?->role === 'admin')
            ? redirect()->route('admin.dashboard')->with('status', 'Welcome back, admin!')
            : redirect()->route('home')->with('status', 'Signed in successfully!');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 🔐 Forget device lock cookie
        $forget = cookie('sid_lock', null, -60,
            config('session.path', '/'),
            config('session.domain', null),
            config('session.secure', false),
            config('session.http_only', true),
            false,
            config('session.same_site', 'lax')
        );

        return redirect()->route('home')
            ->with('status', 'You have been logged out.')
            ->withCookie($forget);
    }

    // Dashboard + Updates
    public function dashboard()
    {
        $localPayments = Payment::where('user_id', Auth::id())
            ->latest('id')
            ->limit(10)
            ->get(['id as payment_id','payment_method','payment_status','payment_date','amount']);

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

    protected function loginThrottleKey(Request $request): string
    {
        $email = Str::lower($request->input('email', 'guest'));
        return 'login:'.$email.'|'.$request->ip();
    }

    /**
     * 🔐 Helper: create + set device lock cookie/session
     */
    private function setDeviceLock(Request $request)
    {
        $lock = bin2hex(random_bytes(32));
        session(['sid_lock' => $lock]);

        $minutes  = (int) config('session.lifetime', 30);
        $path     = config('session.path', '/');
        $domain   = config('session.domain', null);
        $secure   = (bool) config('session.secure', false);
        $httpOnly = (bool) config('session.http_only', true);
        $sameSite = config('session.same_site', 'lax');

        $cookie = cookie('sid_lock', $lock, $minutes, $path, $domain, $secure, $httpOnly, false, $sameSite);

        // attach cookie to response
        cookie()->queue($cookie);
    }
}
