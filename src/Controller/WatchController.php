<?php

namespace App\Controller;

use App\Entity\Watch;
use App\Repository\WatchRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use App\Form\WatchType;

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

    #[Route('/watches/new', name: 'watch_create', methods: ['POST'])]
    public function createWatch(Request $request, EntityManagerInterface $entityManager, Security $security): JsonResponse
    {
        $watch = new Watch();
        $form = $this->createForm(WatchType::class, $watch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $security->getUser();
            $watch->setAuthor($user);

            $entityManager->persist($watch);
            $entityManager->flush();

            return $this->json($watch, 201, [], ['groups' => 'watch:read']);
        }

        return $this->json(['error' => 'Invalid data'], 400);
    }
}