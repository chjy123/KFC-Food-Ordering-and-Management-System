<?php
namespace App\Services\Auth;

use App\Models\User;
use App\Services\Auth\IAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CustomerAuthService implements IAuthService
{
    public function register(array $data): User
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'], // auto-hashed by cast
            'phoneNo'  => $data['phoneNo'] ?? null,
            'role'     => 'customer',
        ]);
    }

    public function login(Request $request): bool
    {
        $remember = (bool) $request->boolean('remember');
        $creds = $request->only('email','password');

        if (!Auth::attempt($creds, $remember)) {
            throw ValidationException::withMessages(['email' => 'Invalid credentials.']);
        }
        $request->session()->regenerate();
        return true;
    }
}
