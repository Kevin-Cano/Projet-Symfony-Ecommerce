<?php

namespace App\Controller;

use App\Entity\Favorite;
use App\Repository\WatchRepository;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/wishlist')]
class WishlistController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private FavoriteRepository $favoriteRepository;
    private WatchRepository $watchRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FavoriteRepository $favoriteRepository,
        WatchRepository $watchRepository
    ) {
        $this->entityManager = $entityManager;
        $this->favoriteRepository = $favoriteRepository;
        $this->watchRepository = $watchRepository;
    }

    #[Route('', name: 'app_wishlist')]
    public function index(): Response
    {
        $user = $this->getUser();
        $wishlistItems = $this->favoriteRepository->findByUser($user);

        return $this->render('wishlist/index.html.twig', [
            'wishlistItems' => $wishlistItems
        ]);
    }

    #[Route('/add/{id}', name: 'app_wishlist_add', methods: ['POST'])]
    public function add(int $id, Request $request): Response
    {
        $user = $this->getUser();
        $watch = $this->watchRepository->find($id);

        if (!$watch) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => false, 'message' => 'Montre non trouvée'], 404);
            }
            $this->addFlash('error', 'Montre non trouvée');
            return $this->redirectToRoute('app_collection');
        }

        // Vérifier si la montre est déjà dans la liste de souhaits
        $existingItem = $this->favoriteRepository->findOneByUserAndWatch($user, $watch);
        
        if ($existingItem) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => false, 'message' => 'Cette montre est déjà dans votre liste de souhaits']);
            }
            $this->addFlash('info', 'Cette montre est déjà dans votre liste de souhaits');
            return $this->redirectToRoute('app_wishlist');
        }

        // Ajouter la montre à la liste de souhaits
        $favorite = new Favorite();
        $favorite->setUser($user);
        $favorite->setWatch($watch);

        $this->entityManager->persist($favorite);
        $this->entityManager->flush();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['success' => true, 'message' => 'Montre ajoutée à votre liste de souhaits']);
        }

        $this->addFlash('success', 'Montre ajoutée à votre liste de souhaits');
        return $this->redirectToRoute('app_wishlist');
    }

    #[Route('/remove/{id}', name: 'app_wishlist_remove', methods: ['POST', 'GET'])]
    public function remove(int $id, Request $request): Response
    {
        $user = $this->getUser();
        $watch = $this->watchRepository->find($id);
        
        if (!$watch) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => false, 'message' => 'Montre non trouvée'], 404);
            }
            $this->addFlash('error', 'Montre non trouvée');
            return $this->redirectToRoute('app_wishlist');
        }
        
        $favorite = $this->favoriteRepository->findOneByUserAndWatch($user, $watch);

        if (!$favorite) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => false, 'message' => 'Élément non trouvé dans votre liste de souhaits'], 404);
            }
            $this->addFlash('error', 'Élément non trouvé dans votre liste de souhaits');
            return $this->redirectToRoute('app_wishlist');
        }

        $this->entityManager->remove($favorite);
        $this->entityManager->flush();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['success' => true, 'message' => 'Montre retirée de votre liste de souhaits']);
        }

        $this->addFlash('success', 'Montre retirée de votre liste de souhaits');
        return $this->redirectToRoute('app_wishlist');
    }

    #[Route('/check/{id}', name: 'app_wishlist_check', methods: ['GET'])]
    public function check(int $id): JsonResponse
    {
        $user = $this->getUser();
        $watch = $this->watchRepository->find($id);
        
        if (!$watch) {
            return new JsonResponse(['inWishlist' => false]);
        }
        
        $favorite = $this->favoriteRepository->findOneByUserAndWatch($user, $watch);

        return new JsonResponse(['inWishlist' => $favorite !== null]);
    }
} 