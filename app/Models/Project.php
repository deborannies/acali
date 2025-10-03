<?php

namespace App\Models;

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

    public function errors($index = null): string|null
    {
        if (isset($this->errors[$index])) {
            return $this->errors[$index];
        }

        return null;
    }

    public function save(): bool
    {
        if ($this->isValid()) {
            if ($this->isNewRecord()) { 
                $this->id = file_exists(self::DB_PATH()) ? count(file(self::DB_PATH())) : 0;
                file_put_contents(self::DB_PATH(), $this->title . PHP_EOL, FILE_APPEND);
            } else {
                $projects = file(self::DB_PATH(), FILE_IGNORE_NEW_LINES);
                $projects[$this->id] = $this->title;

                $data = implode(PHP_EOL, $projects);
                file_put_contents(self::DB_PATH(), $data . PHP_EOL);
            }

            return true;
        }

        return false;
    }

    public function destroy()
    {
        $projects = file(self::DB_PATH(), FILE_IGNORE_NEW_LINES); 
        unset($projects[$this->id]);

        $data = implode(PHP_EOL, $projects);
        
        if (!empty($projects)) {
            $data .= PHP_EOL;
        }
        
        file_put_contents(self::DB_PATH(), $data); 
    }

    private function isNewRecord(): bool
    {
        return $this->id === -1;
    }

    public static function all(): array
    {
        $projects = file(self::DB_PATH(), FILE_IGNORE_NEW_LINES);

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

    private static function DB_PATH()
    {
        return DATABASE_PATH . $_ENV['DB_NAME'];
    }
}
