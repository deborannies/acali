<?php

namespace App\Models;

use Core\Database\Database;
use PDO;

class User
{
    private ?int $id = null;
    private ?string $name = null;
    private ?string $email = null;
    private ?string $encrypted_password = null;
    private string $role = 'user';
    private ?string $password = null;

    /** @var array<string, string> */
    private array $errors = [];

    public static string $table = 'users';

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    // -------------------- Getters --------------------
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

    // -------------------- Setters --------------------
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    // -------------------- Validation --------------------
    public function isValid(): bool
    {
        $this->errors = [];

        if (empty($this->name)) {
            $this->errors['name'] = 'O nome é obrigatório.';
        }

        if (empty($this->email)) {
            $this->errors['email'] = 'O e-mail é obrigatório.';
        }

        if (!$this->id && empty($this->password)) {
            $this->errors['password'] = 'A senha é obrigatória.';
        }

        return empty($this->errors);
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function errors(string $index = null): ?string
    {
        if (isset($this->errors[$index])) {
            return $this->errors[$index];
        }
        return null;
    }

    // -------------------- CRUD --------------------
    public function save(): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        $pdo = Database::getDatabaseConn();

        if ($this->id) {
            // Update
            $stmt = $pdo->prepare(
                "UPDATE users SET name = :name, email = :email, role = :role WHERE id = :id"
            );
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        } else {
            // Insert
            $stmt = $pdo->prepare(
                "INSERT INTO users (name, email, encrypted_password, role) VALUES (:name, :email, :password, :role)"
            );
            $this->encrypted_password = password_hash($this->password ?? '', PASSWORD_DEFAULT);
            $stmt->bindValue(':password', $this->encrypted_password);
        }

        $stmt->bindValue(':name', $this->name);
        $stmt->bindValue(':email', $this->email);
        $stmt->bindValue(':role', $this->role);

        $result = $stmt->execute();

        if (!$this->id) {
            $this->id = (int)$pdo->lastInsertId();
        }

        return $result;
    }

    /**
     * @return User[]
     */
    public static function all(int $limit, int $offset): array
    {
        $pdo = Database::getDatabaseConn();
        $sql = "SELECT * FROM users LIMIT :limit OFFSET :offset;";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $users = [];
        foreach ($rows as $row) {
            $users[] = new self($row);
        }

        return $users;
    }

    public static function countAll(): int
    {
        $pdo = Database::getDatabaseConn();
        $sql = 'SELECT COUNT(id) FROM users;';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public static function findById(int $id): ?self
    {
        $pdo = Database::getDatabaseConn();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new self($row);
        }

        return null;
    }

    public static function find(int $id): ?self
    {
        return self::findById($id);
    }

    public static function findByEmail(string $email): ?self
    {
        $pdo = Database::getDatabaseConn();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new self($row);
        }

        return null;
    }

    public function destroy(): void
    {
        if (!$this->id) {
            return;
        }

        $pdo = Database::getDatabaseConn();
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();

        $this->id = null;
    }

    // -------------------- Auth --------------------
    public function authenticate(string $password): bool
    {
        if ($this->encrypted_password === null) {
            return false;
        }

        return password_verify($password, $this->encrypted_password);
    }
}