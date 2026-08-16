<?php

declare(strict_types=1);

namespace Xchert\PropertyAccess\Test\Classes;

class ChildDemo extends Demo
{
    private int $age = 20;

    public function getAge(): int
    {
        return $this->age;
    }

    public function setAge(int $age): void
    {
        $this->age = $age;
    }
}