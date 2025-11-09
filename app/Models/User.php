<?php

namespace App\Models;

use Core\Database\ActiveRecord\HasMany;
use Core\Database\ActiveRecord\Model;

/**
 * @property string $name
 * @property string $email
 * @property string $encrypted_password
 * @property string $role
 * @property-read array<Project> $projects
 */
class User extends Model
{
    protected static string $table = 'users';
    protected static array $columns = [
        'name',
        'email',
        'encrypted_password',
        'role'
    ];

    public ?string $password = null;

    public function validates(): void
    {
        if (empty($this->name)) {
            $this->addError('name', 'O nome é obrigatório.');
        }

        if (empty($this->email)) {
            $this->addError('email', 'O e-mail é obrigatório.');
        }

        if ($this->newRecord() && empty($this->password)) {
            $this->addError('password', 'A senha é obrigatória.');
        }

        $userExists = User::findBy(['email' => $this->email]);
        if ($userExists && $userExists->id != $this->id) {
            $this->addError('email', 'Este e-mail já está em uso.');
        }
    }

    public function save(): bool
    {
        if (!empty($this->password)) {
            $this->encrypted_password = password_hash($this->password, PASSWORD_DEFAULT);
        }

        $success = parent::save();

        if ($success && !empty($this->password)) {
            $this->password = null;
        }

        return $success;
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'user_id');
    }

    public function authenticate(string $password): bool
    {
        if ($this->encrypted_password === null) {
            return false;
        }

        return password_verify($password, $this->encrypted_password);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public static function find(int $id): ?static
    {
        return self::findById($id);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getRole(): string
    {
        return $this->role;
    }
}
