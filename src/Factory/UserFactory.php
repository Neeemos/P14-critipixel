<?php

namespace App\Factory;

use App\Model\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    private static int $userCount;

    public function __construct()
    {
        self::$userCount = 0;
    }

    public static function class(): string
    {
        return User::class;
    }

    /** @return array<string, mixed> ***/
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
        return self::$userCount++;
    }

    protected function initialize(): static
    {
        return $this;
    }
}
