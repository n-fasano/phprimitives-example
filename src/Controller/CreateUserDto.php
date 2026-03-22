<?php

namespace App\Controller;

use App\Domain\Email;
use App\Domain\Name;

class CreateUserDto
{
    public function __construct(
        public Email $email,
        public Name $name,
    ) {}
}