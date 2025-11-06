<?php

namespace App\Models;

use Core\Database\BelongsTo;
use Core\Database\Database;
use Core\Database\HasMany;
use PDO;

class Project
{
    /** @var array<string, string> */
    private array $errors = [];

    public function __construct(
        private string $title = '',
        private int $user_id = -1,
        private int $id = -1
    ) {
    }

    public function __get(string $name)
    {
        if (method_exists($this, $name)) {
            $method = $this->$name();
            if ($method instanceof HasMany || $method instanceof BelongsTo) {
                return $method->get();
            }
        }
        return null;
    }

    public function user(): BelongsTo
    {
        return new BelongsTo($this, User::class, 'user_id');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function isValid(): bool
    {
        $this->errors = [];
        if (empty($this->title)) {
            $this->errors['title'] = 'não pode ser vazio!';
        }
        if ($this->user_id === -1) {
            $this->errors['user_id'] = 'deve ser associado a um usuário!';
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

    public function save(): bool
    {
        if ($this->isValid()) {
            $pdo = Database::getDatabaseConn();

            if ($this->isNewRecord()) {
                $sql = 'INSERT INTO projects (title, user_id) VALUES (:title, :user_id);';
                $stmt = $pdo->prepare($sql);
            } else {
                $sql = 'UPDATE projects SET title = :title, user_id = :user_id WHERE id = :id;';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            }

            $stmt->bindParam(':title', $this->title);
            $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_INT);
            $stmt->execute();

            if ($this->isNewRecord()) {
                $this->id = (int) $pdo->lastInsertId();
            }

            return true;
        }
        return false;
    }

    public function destroy(): bool
    {
        $pdo = Database::getDatabaseConn();

        $sql = 'DELETE FROM projects WHERE id = :id;';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();

        return ($stmt->rowCount() !== 0);
    }

    private function isNewRecord(): bool
    {
        return $this->id === -1;
    }

    public static function table(): string
    {
        return 'projects';
    }

    public static function columns(): array
    {
        return ['id', 'title', 'user_id'];
    }

    /**
     * @return array<int, Project>
     */
    public static function all(int $limit, int $offset): array
    {
        $projects = [];
        $pdo = Database::getDatabaseConn();
        $sql = 'SELECT * FROM projects LIMIT :limit OFFSET :offset;';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $projects[] = new Project(id: $row['id'], title: $row['title'], user_id: $row['user_id']);
        }

        return $projects;
    }

    public static function countAll(): int
    {
        $pdo = Database::getDatabaseConn();
        $sql = 'SELECT COUNT(id) FROM projects;';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public static function findById(int $id): ?Project
    {
        $pdo = Database::getDatabaseConn();

        $sql = 'SELECT * FROM projects WHERE id = :id;';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            return null;
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return new Project(id: $row['id'], title: $row['title'], user_id: $row['user_id']);
    }

    /**
     * @param array<string, mixed> $conditions
     * @return Project[]
     */
    public static function where(array $conditions): array
    {
        $pdo = Database::getDatabaseConn();
        $sql = 'SELECT * FROM ' . self::table();

        if (!empty($conditions)) {
            $sql .= ' WHERE ';
            $clauses = [];
            foreach ($conditions as $col => $val) {
                $clauses[] = "$col = :$col";
            }
            $sql .= implode(' AND ', $clauses);
        }

        $stmt = $pdo->prepare($sql);
        foreach ($conditions as $col => $val) {
            $stmt->bindValue(":$col", $val);
        }
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $projects = [];
        foreach ($rows as $row) {
            $projects[] = new Project(id: $row['id'], title: $row['title'], user_id: $row['user_id']);
        }

        return $projects;
    }
}