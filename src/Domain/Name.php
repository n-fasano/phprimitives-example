<?php

namespace App\Domain;

use Fasano\PHPrimitives\AbstractString;
use InvalidArgumentException;

readonly class Name extends AbstractString
{
    protected static function validate(string $value): void
    {
        if (\strlen($value) < 5) {
            throw new InvalidArgumentException('Name must be at least 5 characters long.');
        }
    }
}