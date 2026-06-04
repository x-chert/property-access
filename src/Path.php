<?php

namespace Xchert\PropertyAccess;

use Xchert\PropertyAccess\Exception\InvalidPathException;
use Xchert\Util\Json;
use Xchert\Util\Type;
use Xchert\Util\Value;

class Path implements \IteratorAggregate, \Stringable, \JsonSerializable
{
    private array $path = [];

    /**
     * @throws InvalidPathException
     */
    public function __construct(array $path = [])
    {
        $this->setPath($path);
    }

    public static function isValid(string $path): bool {
        try {
            $path = Json::decode($path);
        } catch(\JsonException) {
            return false;
        }

        if(!\is_array($path) || !\array_is_list($path)) {
            return false;
        }

        foreach($path as $field) {
            try {
                self::validateField($field);
            } catch(InvalidPathException) {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws InvalidPathException
     * @throws \JsonException
     */
    public static function ensure(string|self $path): Path {
        if(\is_string($path)) {
            $path = self::parse($path);
        }

        return $path;
    }

    /**
     * @throws \JsonException
     * @throws InvalidPathException
     */
    public static function parse(string $path): self
    {
        $path = Json::decode($path);

        return new self($path);
    }

    public function getIterator(): \Traversable
    {
        yield from $this->path;
    }

    public function getLength(): int
    {
        return \count($this->path);
    }

    public function isEmpty(): bool
    {
        return empty($this->path);
    }

    /**
     * @throws InvalidPathException
     */
    public function add(string $field): self
    {
        self::validateField($field);
        $this->path[] = $field;

        return $this;
    }

    public function merge(self $path): self
    {
        $new = new Path($this->path);

        foreach($path as $field) {
            $new->add($field);
        }

        return $new;
    }

    public function copy(): self
    {
        return new Path($this->path);
    }

    public function pop(): ?string
    {
        return \array_pop($this->path);
    }

    public function shift(): ?string
    {
        return \array_shift($this->path);
    }

    public function toArray(): array
    {
        return $this->path;
    }

    public function equals(self $path): bool
    {
        if($this->getLength() !== $path->getLength()) {
            return false;
        }

        return empty(\array_diff_assoc($this->path, $path->toArray()));
    }

    public function jsonSerialize(): mixed
    {
        return \array_values($this->path);
    }

    public function __toString(): string
    {
        return Json::encode($this);
    }

    /**
     * @throws InvalidPathException
     */
    private function setPath(array $path): void
    {
        $path = \array_values($path);

        foreach($path as $index => $field) {
            self::validateField($field, $index);
            $this->path[] = (string) $field;
        }
    }

    /**
     * @throws InvalidPathException
     */
    private static function validateField(mixed $field, ?int $position = null): void
    {
        if(!\is_string($field) && !Type::isStringConvertable(Type::getType($field))) {
            throw InvalidPathException::invalidPathElement($field);
        }

        if(Value::isEmpty((string) $field)) {
            throw InvalidPathException::emptyField($position);
        }
    }
}
