<?php

namespace App\Models;

use Core\Constants\Constants;

class Project
{
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

    public function setTitle(string $title)
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

    public function errors($index = null): ?string
    {
        if (isset($this->errors[$index])) {
            return $this->errors[$index];
        }
        return null;
    }

    public function save(): bool
    {
        if ($this->isValid()) {
            $databasePath = Constants::databasePath();
            if (!is_dir((string)$databasePath)) {
                mkdir((string)$databasePath, 0777, true);
            }

            if ($this->isNewRecord()) {
                $this->id = file_exists(self::dbPath()) ? count(file(self::dbPath())) : 0;
                file_put_contents(self::dbPath(), $this->title . PHP_EOL, FILE_APPEND);
            } else {
                $projects = file(self::dbPath(), FILE_IGNORE_NEW_LINES);
                $projects[$this->id] = $this->title;
                $data = implode(PHP_EOL, $projects);
                file_put_contents(self::dbPath(), $data . PHP_EOL);
            }
            return true;
        }
        return false;
    }

    public function destroy()
    {
        $projects = file(self::dbPath(), FILE_IGNORE_NEW_LINES);
        unset($projects[$this->id]);
        $data = implode(PHP_EOL, $projects);
        if (!empty($projects)) {
            $data .= PHP_EOL;
        }
        file_put_contents(self::dbPath(), $data);
    }

    private function isNewRecord(): bool
    {
        return $this->id === -1;
    }

    public static function all(): array
    {
        if (!file_exists(self::dbPath())) {
            return [];
        }
        $projects = file(self::dbPath(), FILE_IGNORE_NEW_LINES);
        return array_map(function ($line, $title) {
            return new Project(id: $line, title: $title);
        }, array_keys($projects), $projects);
    }

    public static function findById(int $id): ?Project
    {
        $projects = self::all();
        foreach ($projects as $project) {
            if ($project->getId() === $id) {
                return $project;
            }
        }
        return null;
    }

    private static function dbPath()
    {
        return Constants::databasePath()->join($_ENV['DB_NAME']);
    }
}
