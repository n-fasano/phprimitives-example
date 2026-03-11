<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/users', name: 'users.create', methods: ['POST'])]
class CreateUser extends AbstractController
{
    public function __construct(
        protected UserRepository $users,
    ) {}

    public function __invoke(#[MapRequestPayload] CreateUserDto $data): JsonResponse
    {
        $user = new User($data->email);

        $this->users->save($user);

        return new JsonResponse($user);
    }
}