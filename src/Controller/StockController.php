<?php

namespace App\Controller;

use App\Entity\Watch;
use App\Repository\WatchRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/stock', name: 'api_stock_')]
class StockController extends AbstractController
{
    private $entityManager;
    private $watchRepository;

    public function __construct(EntityManagerInterface $entityManager, WatchRepository $watchRepository)
    {
        $this->entityManager = $entityManager;
        $this->watchRepository = $watchRepository;
    }

    #[Route('/add/{id}', name: 'add', methods: ['POST'])]
    public function addStock(Watch $watch, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $quantity = $data['quantity'] ?? 1;

        if ($quantity <= 0) {
            return $this->json([
                'error' => 'La quantité doit être supérieure à 0'
            ], 400);
        }

        $watch->addToStock($quantity);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Stock ajouté avec succès',
            'watch' => $watch
        ], 200, [], ['groups' => 'watch:read']);
    }

    #[Route('/remove/{id}', name: 'remove', methods: ['POST'])]
    public function removeStock(Watch $watch, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $quantity = $data['quantity'] ?? 1;

        if ($quantity <= 0) {
            return $this->json([
                'error' => 'La quantité doit être supérieure à 0'
            ], 400);
        }

        if ($watch->getStock() < $quantity) {
            return $this->json([
                'error' => 'Stock insuffisant'
            ], 400);
        }

        $watch->removeFromStock($quantity);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Stock retiré avec succès',
            'watch' => $watch
        ], 200, [], ['groups' => 'watch:read']);
    }

    #[Route('/set/{id}', name: 'set', methods: ['POST'])]
    public function setStock(Watch $watch, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $quantity = $data['quantity'] ?? 0;

        if ($quantity < 0) {
            return $this->json([
                'error' => 'La quantité ne peut pas être négative'
            ], 400);
        }

        $watch->setStock($quantity);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Stock mis à jour avec succès',
            'watch' => $watch
        ], 200, [], ['groups' => 'watch:read']);
    }

    #[Route('/low-stock', name: 'low_stock', methods: ['GET'])]
    public function getLowStock(): JsonResponse
    {
        $watches = $this->watchRepository->findBy(['isAvailable' => true]);
        $lowStockWatches = array_filter($watches, function($watch) {
            return $watch->getStock() < 5; // Seuil de stock bas fixé à 5
        });

        return $this->json([
            'watches' => array_values($lowStockWatches)
        ], 200, [], ['groups' => 'watch:read']);
    }
} 