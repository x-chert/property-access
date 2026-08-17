<?php

declare(strict_types=1);

namespace Xchert\PropertyAccess\Test\Data;

class FileDataProvider
{
    public static function propertyaccessor_get(): iterable
    {
        return static::readFile(__DIR__.'/propertyaccessor_get.php');
    }

    public static function propertyaccessor_set(): iterable
    {
        return static::readFile(__DIR__.'/propertyaccessor_set.php');
    }

    public static function propertyaccessor_push(): iterable
    {
        return static::readFile(__DIR__.'/propertyaccessor_push.php');
    }

    public static function propertyaccessor_merge(): iterable
    {
        return static::readFile(__DIR__.'/propertyaccessor_merge.php');
    }

    public static function propertyaccessor_collect(): iterable
    {
        return static::readFile(__DIR__.'/propertyaccessor_collect.php');
    }

    public static function propertyaccessor_has(): iterable
    {
        return static::readFile(__DIR__.'/propertyaccessor_has.php');
    }

    public static function readFile(string $file): iterable
    {
        if (!\file_exists($file) || !\is_readable($file)) {
            throw new \RuntimeException(\sprintf('File %s does not exist or is not readable.', $file));
        }

        return require $file;
    }
}