<?php

namespace App\Infrastructure;

use App\Domain\Name;
use Fasano\PHPrimitives\Doctrine\StringPrimitiveType;

class NameType extends StringPrimitiveType
{
    public const string NAME = 'name';

    public function getName(): string
    {
        return NameType::NAME;
    }

    protected function getPrimitiveFqcn(): string
    {
        return Name::class;
    }
}