<?php

namespace Lib;

use Core\Database\Database;
use PDO;

class Paginator
{
    private string $class;
    private int $page;
    private int $per_page;
    private string $table;
    private array $attributes;
    private ?string $route;
    private array $conditions;

    private int $total_rows = 0;
    private int $total_pages = 0;
    private array $items = [];

    /**
     * @param class-string $class        
     * @param int $page
     * @param int $per_page
     * @param string $table
     * @param array<int, string> $attributes
     * @param string|null $route
     * @param array<string, mixed> $conditions
     */
    public function __construct(
        string $class,
        int $page,
        int $per_page,
        string $table,
        array $attributes,
        ?string $route = null,
        array $conditions = []
    ) {
        $this->class = $class;
        $this->page = $page;
        $this->per_page = $per_page;
        $this->table = $table;
        $this->attributes = $attributes;
        $this->route = $route;
        $this->conditions = $conditions;

        $this->total_rows = $this->countTotalRows();
        $this->total_pages = (int) ceil($this->total_rows / $this->per_page);
        $this->items = $this->fetchItems();
    }

    private function countTotalRows(): int
    {
        $pdo = Database::getDatabaseConn();
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        
        $sqlConditions = $this->getSqlConditions();
        $sql .= $sqlConditions;

        $stmt = $pdo->prepare($sql);
        foreach ($this->conditions as $column => $value) {
            $stmt->bindValue($column, $value);
        }

        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    private function fetchItems(): array
    {
        $pdo = Database::getDatabaseConn();
        $offset = ($this->page - 1) * $this->per_page;
        $attributes = implode(', ', $this->attributes);

        $sql = "SELECT id, {$attributes} FROM {$this->table}";
        
        $sqlConditions = $this->getSqlConditions();
        $sql .= $sqlConditions;

        $sql .= " ORDER BY id DESC LIMIT {$this->per_page} OFFSET {$offset}";

        $stmt = $pdo->prepare($sql);
        foreach ($this->conditions as $column => $value) {
            $stmt->bindValue($column, $value);
        }
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $models = [];
        foreach ($rows as $row) {
            $models[] = new $this->class($row);
        }
        return $models;
    }
    
    private function getSqlConditions(): string
    {
        if (empty($this->conditions)) {
            return '';
        }

        $sqlConditions = array_map(function ($column) {
            return " {$column} = :{$column}";
        }, array_keys($this->conditions));
        
        return ' WHERE ' . implode(' AND ', $sqlConditions);
    }

    /**
     * @return array<mixed>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getTotalPages(): int
    {
        return $this->total_pages;
    }

    public function getCurrentPage(): int
    {
        return $this->page;
    }

    public function hasPreviousPage(): bool
    {
        return $this->page > 1;
    }

    public function hasNextPage(): bool
    {
        return $this->page < $this->total_pages;
    }

    public function getPreviousPage(): int
    {
        return $this->page - 1;
    }

    public function getNextPage(): int
    {
        return $this->page + 1;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }
}