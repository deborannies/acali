<?php

namespace Lib;

use Core\Database\Database;
use PDO;

class Paginator
{
    private int $totalPages;
    /** @var array<mixed> */
    private array $items = [];

    /**
     * @param string $model
     * @param array<string, mixed> $conditions
     * @param int $itemsPerPage
     * @param int $currentPage
     */
    public function __construct(
        private string $model,
        private array $conditions = [],
        private int $itemsPerPage = 10,
        private int $currentPage = 1
    ) {
        $this->validateCurrentPage();
        $this->calculateTotalPages();
        $this->fetchItems();
    }

    private function validateCurrentPage(): void
    {
        if ($this->currentPage < 1) {
            $this->currentPage = 1;
        }
    }

    private function calculateTotalPages(): void
    {
        $pdo = Database::getDatabaseConn();
        $sql = "SELECT COUNT(*) FROM " . ($this->model)::table();
        $sql .= $this->buildConditions();

        $stmt = $pdo->prepare($sql);
        $this->bindConditions($stmt);
        $stmt->execute();

        $totalItems = (int) $stmt->fetchColumn();
        $this->totalPages = (int) ceil($totalItems / $this->itemsPerPage);

        if ($this->currentPage > $this->totalPages && $this->totalPages > 0) {
            $this->currentPage = $this->totalPages;
        } elseif ($this->totalPages === 0) {
            $this->currentPage = 1;
        }
    }

    private function fetchItems(): void
    {
        if ($this->totalPages === 0) {
            return;
        }

        $pdo = Database::getDatabaseConn();
        $sql = "SELECT * FROM " . ($this->model)::table();
        $sql .= $this->buildConditions();
        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);
        $this->bindConditions($stmt);
        $stmt->bindValue(':limit', $this->itemsPerPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $this->getOffset(), PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $this->items[] = new ($this->model)(...$row);
        }
    }

    private function buildConditions(): string
    {
        if (empty($this->conditions)) {
            return '';
        }

        $clauses = [];
        foreach ($this->conditions as $field => $value) {
            $clauses[] = "$field = :$field";
        }

        return ' WHERE ' . implode(' AND ', $clauses);
    }

    private function bindConditions(\PDOStatement $stmt): void
    {
        foreach ($this->conditions as $field => $value) {
            $stmt->bindValue(":$field", $value);
        }
    }

    private function getOffset(): int
    {
        return ($this->currentPage - 1) * $this->itemsPerPage;
    }

    /** @return array<mixed> */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->totalPages;
    }

    public function getPreviousPage(): int
    {
        return $this->currentPage - 1;
    }

    public function getNextPage(): int
    {
        return $this->currentPage + 1;
    }

    /**
     * @param array<string, mixed> $routeParams
     */
    public function render(string $routeName, array $routeParams = []): void
    {
        if ($this->getTotalPages() <= 1) {
            return;
        }

        $paginator = $this;
        $params = $routeParams;
        require '/var/www/app/views/layouts/_pages.phtml';
    }
}