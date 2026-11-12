<?php

declare(strict_types=1);

namespace Xchert\PropertyAccess\Test\Classes;

class Demo
{
    private string $name = 'John';

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}