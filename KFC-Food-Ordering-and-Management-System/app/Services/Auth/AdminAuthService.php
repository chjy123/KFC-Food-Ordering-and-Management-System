<?php
namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminAuthService implements IAuthService
{
    public function register(array $data): User
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'], // auto-hashed by cast
            'phoneNo'  => $data['phoneNo'] ?? null,
            'role'     => 'admin',
        ]);
    }

    public function login(Request $request): bool
    {
        $creds = $request->only('email','password');
        if (!Auth::attempt($creds, false)) {
            throw ValidationException::withMessages(['email' => 'Invalid credentials.']);
        }
        $request->session()->regenerate();
        return true;
    }
}
