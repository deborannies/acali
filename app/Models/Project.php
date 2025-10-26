<?php

namespace App\Models;

use Core\Database\Database;
use PDO;

class Project
{
    /** @var array<string, string> */
    private array $errors = [];

    public function __construct(
        private string $title = '',
        private int $id = -1
    ) {
    }

    public function getId(): int
    {
        return $this->id;
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
                $sql = 'INSERT INTO projects (title) VALUES (:title);';
                $stmt = $pdo->prepare($sql);
            } else {
                $sql = 'UPDATE projects SET title = :title WHERE id = :id;';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            }

            $stmt->bindParam(':title', $this->title);
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

    /**
     * @return array<int, Project>
     */
    public static function all(int $limit, int $offset): array
    {
        $projects = [];
        $pdo = Database::getDatabaseConn();
        $sql = 'SELECT id, title FROM projects LIMIT :limit OFFSET :offset;';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $projects[] = new Project(id: $row['id'], title: $row['title']);
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

        $sql = 'SELECT id, title FROM projects WHERE id = :id;';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            return null;
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return new Project(id: $row['id'], title: $row['title']);
    }
}