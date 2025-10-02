<?php

class Project
{
    // 1. Alteramos o caminho para o arquivo do seu banco de dados
    const DB_PATH = '/var/www/database/projects.txt';

    private array $errors = [];

    // O construtor permanece o mesmo, recebendo um título
    public function __construct(
        private string $title = '',
        private int $id = -1
    ) {
    }

    // Todos os métodos abaixo são idênticos ao do professor,
    // pois a lógica de validação e salvamento é a mesma por enquanto.

    public function setID(int $id)
    {
        $this->id = $id;
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

        if (empty($this->title))
            $this->errors['title'] = 'não pode ser vazio!';

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
                // Lógica para CRIAR um novo registro (adiciona no final do arquivo)
                $this->id = count(file(self::DB_PATH));
                file_put_contents(self::DB_PATH, $this->title . PHP_EOL, FILE_APPEND);
            } else {
                // Lógica para ATUALIZAR um registro existente
                $projects = file(self::DB_PATH, FILE_IGNORE_NEW_LINES);
                $projects[$this->id] = $this->title;

                $data = implode(PHP_EOL, $projects);
                file_put_contents(self::DB_PATH, $data . PHP_EOL);
            }
            return true;
        }

        return false;
    }
    
    private function isNewRecord(): bool
    {
        return $this->id === -1;
    }

    public static function all(): array
    {
        $projects = file(self::DB_PATH, FILE_IGNORE_NEW_LINES);

        return array_map(function ($line, $title) {
            return new Project(id: $line, title: $title);
        }, array_keys($projects), $projects);
    }

    public static function findById(int $id): Project|null
    {
        $projects = self::all();

        foreach ($projects as $project) {
            if ($project->getId() === $id)
                return $project;
        }
        return null;
    }

    public function destroy()
    {
        $projects = file(self::DB_PATH, FILE_IGNORE_NEW_LINES);
        unset($projects[$this->id]);

        $data = implode(PHP_EOL, $projects);
        
        // Adiciona uma quebra de linha no final, se o arquivo não estiver vazio
        if (!empty($projects)) {
            $data .= PHP_EOL;
        }
        
        file_put_contents(self::DB_PATH, $data);
    }
}