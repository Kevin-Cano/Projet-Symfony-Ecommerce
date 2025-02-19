<?php

namespace App\Controller;

use App\Repository\WatchRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(WatchRepository $watchRepository): Response
    {
        // Récupérer toutes les montres
        $allWatches = $watchRepository->findAll();
        
        // Trier les montres par prix (du plus cher au moins cher)
        usort($allWatches, function($a, $b) {
            // Extraire les valeurs numériques des prix
            $priceA = (float) str_replace(['€', ',', ' '], ['', '.', ''], $a->getPrice());
            $priceB = (float) str_replace(['€', ',', ' '], ['', '.', ''], $b->getPrice());
            
            return $priceB <=> $priceA; // Tri décroissant
        });
        
        // Prendre les 9 premières montres
        $watches = array_slice($allWatches, 0, 9);

        return $this->render('home/index.html.twig', [
            'watches' => $watches,
        ]);
    }
}
