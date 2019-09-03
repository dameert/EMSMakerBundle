<?php

namespace EMS\MakerBundle\Maker;

class FileNames
{
    /** @var array */
    private $names = [];

    public function addName(string $name): void
    {
        $this->names[] = $name;
    }

    public function toArray(): array
    {
        return $this->names;
    }

    public function __toString(): string
    {
        return implode(', ', $this->names);
    }
}
