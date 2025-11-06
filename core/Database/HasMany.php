<?php

namespace Core\Database;

class HasMany
{
    public function __construct(
        private object $origin,
        private string $related,
        private string $foreignKey
    ) {
    }

    public function get(): array
    {
        return ($this->related)::where([$this->foreignKey => $this->origin->getId()]);
    }

    public function new(): object
    {
        $object = new $this->related();
        $setter = 'set' . str_replace('_', '', ucwords($this->foreignKey, '_'));

        if (method_exists($object, $setter)) {
            $object->$setter($this->origin->getId());
        }

        return $object;
    }

    public function find_by_id(int $id): ?object
    {
        $result = ($this->related)::where([
            'id' => $id,
            $this->foreignKey => $this->origin->getId()
        ]);

        return $result[0] ?? null;
    }
}