<?php

namespace App\Domain;

use Fasano\PHPrimitives\AbstractString;

readonly class Email extends AbstractString
{
    protected static function validate(string $value): void
    {
    }
}