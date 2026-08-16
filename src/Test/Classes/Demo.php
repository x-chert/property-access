<?php

declare(strict_types=1);

namespace Xchert\PropertyAccess\Test\Classes;

class Demo
{
    public string $birthday = '1990-01-01';
    private string $name = 'John';
    private bool $gotName = false;
    private bool $isSetName = false;

    public function isSetName(): bool
    {
        return $this->isSetName;
    }

    public function getName(): string
    {
        $this->gotName = true;
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->isSetName = true;
        $this->name = $name;
    }

    public function isGotName(): bool
    {
        return $this->gotName;
    }
}