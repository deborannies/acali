<?php

namespace App\Models;

use Core\Database\Database;
use PDO;
use PDOException;

class User
{
    public int $id;
    public string $email;
    public string $encrypted_password;
    public ?string $name = null;

    public ?string $password = null;
    public array $errors = [];

    public static string $table = 'users';

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

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /** Cria um novo usuário */
    public function save(): bool
    {
        $this->validate();

        if (!empty($this->errors)) {
            return false;
        }

        $pdo = Database::getDatabaseConn();

        $stmt = $pdo->prepare("INSERT INTO users (name, email, encrypted_password) VALUES (:name, :email, :password)");
        $stmt->bindValue(':name', $this->name);
        $stmt->bindValue(':email', $this->email);
        $stmt->bindValue(':password', password_hash($this->password, PASSWORD_DEFAULT));

        return $stmt->execute();
    }

    /** Procura um usuário pelo e-mail */
    public static function findByEmail(string $email): ?User
    {
        $pdo = Database::getDatabaseConn();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return new User($result);
        }

        return null;
    }

    /**
     * ALTERAÇÃO 1: MÉTODO ADICIONADO
     * Procura um usuário pelo seu ID
     */
    public static function find(int $id): ?User
    {
        $pdo = Database::getDatabaseConn();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return new User($result);
        }

        return null;
    }

    /**
     * ALTERAÇÃO 2: MÉTODO CORRIGIDO
     * Autentica o usuário comparando a senha fornecida com a senha criptografada
     */
    public function authenticate(string $password): bool
    {
        return password_verify($password, $this->encrypted_password);
    }

    /** Valida campos obrigatórios */
    public function validate(): void
    {
        if (empty($this->email)) {
            $this->errors['email'] = 'O e-mail é obrigatório.';
        }

        if (empty($this->password)) {
            $this->errors['password'] = 'A senha é obrigatória.';
        }
    }
}