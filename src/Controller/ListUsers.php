<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/users', name: 'users.list', methods: ['GET'])]
class ListUsers extends AbstractController
{
    public function __construct(
        protected UserRepository $users,
    ) {}

    public function __invoke(): JsonResponse
    {
        return new JsonResponse($this->users->findAll());
    }
}