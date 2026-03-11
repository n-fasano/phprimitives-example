<?php

namespace App\Domain;

use Fasano\PHPrimitives\AbstractString;
use Fasano\PHPrimitives\Exception\InvalidBackingValue;

readonly class Email extends AbstractString
{
    protected static function validate(string $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidBackingValue($value, static::class);
        }
    }
}