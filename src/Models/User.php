<?php
declare(strict_types=1);
namespace App\Models;

abstract class User
{
    public function __construct(
        protected string $id,
        protected string $name,
        protected string $email,
        protected string $passwordHash
    ) {}
    public function getId(): string { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }
    abstract public function getRole(): string;
    public function toArray(): array
    {
        return ['id'=>$this->id, 'name'=>$this->name, 'email'=>$this->email,
            'password'=>$this->passwordHash, 'role'=>$this->getRole()];
    }
    public static function fromArray(array $data): User
    {
        $class = ($data['role'] ?? 'member') === 'admin' ? Admin::class : Member::class;
        return new $class((string)$data['id'], (string)$data['name'],
            (string)$data['email'], (string)$data['password']);
    }
}
