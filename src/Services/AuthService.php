<?php
declare(strict_types=1);
namespace App\Services;

use App\Models\User;

final class AuthService
{
    public function __construct(private JsonStorage $storage) {}
    public function login(string $email, string $password): ?User
    {
        foreach ($this->storage->read('users.json') as $row) {
            $user = User::fromArray($row);
            if (strtolower($user->getEmail()) === strtolower(trim($email)) && $user->verifyPassword($password)) {
                return $user;
            }
        }
        return null;
    }
    public function register(string $name, string $email, string $password): User
    {
        $rows = $this->storage->read('users.json');
        foreach ($rows as $row) if (strtolower($row['email']) === strtolower($email)) {
            throw new \DomainException('Email sudah terdaftar.');
        }
        $user = new \App\Models\Member(uniqid('USR'), $name, $email, password_hash($password, PASSWORD_DEFAULT));
        $rows[] = $user->toArray();
        $this->storage->write('users.json', $rows);
        return $user;
    }
}
