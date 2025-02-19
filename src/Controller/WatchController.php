<?php

namespace App\Controller;

use App\Entity\Watch;
use App\Repository\WatchRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class WatchController extends AbstractController
{
    #[Route('/watches', name: 'watch_list', methods: ['GET'])]
    public function list(WatchRepository $watchRepository): JsonResponse
    {
        $watches = $watchRepository->findAll();
        
        return $this->json($watches, 200, [], ['groups' => 'watch:read']);
    }

    #[Route('/watches/{id}', name: 'watch_show', methods: ['GET'])]
    public function show(Watch $watch): JsonResponse
    {
        return $this->json($watch, 200, [], ['groups' => 'watch:read']);
    }
}
