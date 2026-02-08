<?php

declare(strict_types=1);

namespace App\Presentation\Http\Api\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/marketplace/profile')]
class ProfileController extends AbstractController
{
    #[Route('/whoami', name: 'whoami', methods: ['GET'])]
    public function whoami(): JsonResponse
    {
        return $this->json(['test']);
    }
}
