<?php

namespace App\Controller;

use App\Entity\Watch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupérer uniquement les montres de la boutique (sans author)
        $watches = $entityManager->getRepository(Watch::class)
            ->createQueryBuilder('w')
            ->where('w.author IS NULL')
            ->orderBy('w.id', 'DESC')
            ->setMaxResults(6)  // Limite à 6 montres pour le carrousel
            ->getQuery()
            ->getResult();

        // Récupérer les montres des particuliers séparément
        $privateWatches = $entityManager->getRepository(Watch::class)
            ->createQueryBuilder('w')
            ->where('w.author IS NOT NULL')
            ->orderBy('w.id', 'DESC')
            ->setMaxResults(3)  // Limite à 3 montres pour la section particuliers
            ->getQuery()
            ->getResult();

        return $this->render('home/index.html.twig', [
            'watches' => $watches,
            'privateWatches' => $privateWatches,
        ]);
    }
}
