<?php

namespace App\Factory;

use App\Model\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class UserFactory extends PersistentProxyObjectFactory
{
    private static ?int $userCount = null;

    public static function class(): string
    {
        return User::class;
    }

    protected function defaults(): array
    {
        $count = self::getAndIncrementUserCount();
        return [
            'email' => sprintf('user+%d@email.com', $count),
            'username' => sprintf('user+%d', $count),
            'plainPassword' => 'password',
            'password' => password_hash('password', PASSWORD_BCRYPT),
        ];
    }

    private static function getAndIncrementUserCount(): int
    {
        if (self::$userCount === null) {
            self::$userCount = 0;
        }
        return self::$userCount++;
    }
    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function (User $user): void {});
    }
}
