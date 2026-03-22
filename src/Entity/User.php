<?php

namespace App\Entity;

use App\Domain\Email;
use App\Domain\Name;
use App\Infrastructure\EmailType;
use App\Infrastructure\NameType;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    public readonly UuidV4 $id;

    #[ORM\Column(type: EmailType::NAME, length: 255)]
    public readonly Email $email;

    #[ORM\Column(type: NameType::NAME, length: 255)]
    public readonly Name $name;

    public function __construct(Email $email, Name $name)
    {
        $this->id = Uuid::v4();
        $this->email = $email;
        $this->name = $name;
    }
}
