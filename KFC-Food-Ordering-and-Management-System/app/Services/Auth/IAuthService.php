<?php
// Author's Name: Chow Jun Yu
namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Http\Request;

interface IAuthService
{
    public function register(array $data): User;
    public function login(Request $request): bool;
}