<?php

namespace Lib;

class Paginator
{
    /** @var array<mixed> */
    private array $items;
    private int $totalItems;
    private int $itemsPerPage;
    private int $currentPage;
    private int $totalPages;

    /**
     * @param array<mixed> $items
     */
    public function __construct(array $items, int $totalItems, int $itemsPerPage = 10, int $currentPage = 1)
    {
        $this->items = $items;
        $this->totalItems = $totalItems;
        $this->itemsPerPage = $itemsPerPage;
        $this->currentPage = $currentPage;
        $this->totalPages = (int) ceil($totalItems / $itemsPerPage);
    }

    /**
     * @return array<mixed>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
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