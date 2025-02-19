<?php

namespace App\Controller;

use App\Repository\WatchRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CollectionController extends AbstractController
{
    #[Route('/collection', name: 'app_collection')]
    public function index(WatchRepository $watchRepository): Response
    {
        $watches = $watchRepository->findAll();

        return $this->render('collection/index.html.twig', [
            'watches' => $watches,
        ]);
    }
}
