<?php

namespace App\Models;

use Core\Database\Database;

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
                $stmt->bindParam(':title', $this->title);
                $stmt->execute();

                $this->id = (int) $pdo->lastInsertId();
            } else {
                $sql = 'UPDATE projects SET title = :title WHERE id = :id;';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':title', $this->title);
                $stmt->bindParam(':id', $this->id);
                $stmt->execute();
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
        $stmt->bindParam(':id', $this->id);
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
    public static function all(): array
    {
        $projects = [];
        $pdo = Database::getDatabaseConn();
        $resp = $pdo->query('SELECT id, title FROM projects;');

        foreach ($resp as $row) {
            $projects[] = new Project(id: $row['id'], title: $row['title']);
        }

        return $projects;
    }

    public static function findById(int $id): ?Project
    {
        $pdo = Database::getDatabaseConn();

        $sql = 'SELECT id, title FROM projects WHERE id = :id;';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            return null;
        }

        $row = $stmt->fetch();

        return new Project(id: $row['id'], title: $row['title']);
    }
}
