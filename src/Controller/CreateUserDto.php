<?php

namespace App\Controller;

use App\Domain\Email;

class CreateUserDto
{
    public function __construct(
        public Email $email,
    ) {}
}