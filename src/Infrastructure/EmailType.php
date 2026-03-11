<?php

namespace App\Infrastructure;

use App\Domain\Email;
use Fasano\PHPrimitives\Doctrine\StringPrimitiveType;

class EmailType extends StringPrimitiveType
{
    public const string NAME = 'email';

    public function getName(): string
    {
        return EmailType::NAME;
    }

    protected function getPrimitiveFqcn(): string
    {
        return Email::class;
    }
}