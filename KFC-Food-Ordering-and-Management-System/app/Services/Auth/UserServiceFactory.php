<?php
// Author's Name: Chow Jun Yu
namespace App\Services\Auth;

use InvalidArgumentException;

class UserServiceFactory
{
    public function forRole(?string $role): IAuthService
    {
        $role = $role ? strtolower($role) : 'customer';

        return match ($role) {
            'admin'    => app(AdminAuthService::class),
            'customer' => app(CustomerAuthService::class),
            default    => throw new InvalidArgumentException("Unsupported role: {$role}"),
        };
    }
}
