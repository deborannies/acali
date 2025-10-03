<?php

namespace Core\Constants;

class StringPath
{
    public function __construct(
        private string $path
    ) {
    }

    public function join(string $path): self
    {
        $this->path .= '/' . ltrim($path, '/');

        return $this;
    }

    public function __toString(): string
    {
        return $this->path;
    }
}
