<?php

namespace Core\Database;

class BelongsTo
{
    public function __construct(
        private object $origin,
        private string $related,
        private string $foreignKey
    ) {
    }

    public function get(): ?object
    {
        $getter = 'get' . str_replace('_', '', ucwords($this->foreignKey, '_'));
        if (method_exists($this->origin, $getter)) {
            $foreignKeyValue = $this->origin->$getter();
            return ($this->related)::findById($foreignKeyValue);
        }
        return null;
    }
}