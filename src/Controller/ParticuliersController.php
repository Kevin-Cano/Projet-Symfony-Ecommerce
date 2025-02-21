<?php

namespace App\Controller;

use App\Repository\WatchRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticuliersController extends AbstractController
{
    #[Route('/particuliers', name: 'app_particuliers')]
    public function index(WatchRepository $watchRepository): Response
    {
        // Récupérer les montres qui ont un auteur (montres des particuliers)
        $watches = $watchRepository->findPrivateWatches();

        return $this->render('particuliers/index.html.twig', [
            'watches' => $watches,
        ]);
    }
}
