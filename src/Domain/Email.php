<?php

namespace App\Domain;

use Fasano\PHPrimitives\AbstractString;
use InvalidArgumentException;

readonly class Email extends AbstractString
{
    protected static function validate(string $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid email address: %s',
                $value,
            ));
        }
    }
}