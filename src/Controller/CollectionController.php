<?php

namespace App\Controller;

use App\Repository\WatchRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

class CollectionController extends AbstractController
{
    #[Route('/collection', name: 'app_collection')]
    public function index(Request $request, WatchRepository $watchRepository, PaginatorInterface $paginator): Response
    {
        $query = $watchRepository->findShopWatches();
        
        $pagination = $paginator->paginate(
            $query, // Query
            $request->query->getInt('page', 1), // Page number
            9 // Limit per page
        );

        return $this->render('collection/index.html.twig', [
            'pagination' => $pagination
        ]);
    }
}
