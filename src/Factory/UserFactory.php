<?php

namespace App\Factory;

use App\Model\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class UserFactory extends PersistentProxyObjectFactory
{
    private static int $userCount = 0;

    public static function class(): string
    {
        return User::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'email' => sprintf('user+%d@email.com', self::$userCount++),
            'username' => sprintf('user+%d', self::$userCount),
            'plainPassword' => 'password',
            'password' => password_hash('password', PASSWORD_BCRYPT),
        ];
    }


    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function(User $user): void {
            })
        ;
    }
}