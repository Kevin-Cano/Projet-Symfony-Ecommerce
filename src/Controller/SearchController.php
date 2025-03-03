<?php

namespace App\Controller;

use App\Repository\WatchRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    private $watchRepository;

    public function __construct(WatchRepository $watchRepository)
    {
        $this->watchRepository = $watchRepository;
    }

    #[Route('/search', name: 'app_search')]
    public function index(Request $request): Response
    {
        $query = $request->query->get('q', '');
        
        if (empty($query)) {
            return $this->redirectToRoute('app_home');
        }
        
        $watches = $this->watchRepository->searchWatches($query);
        
        return $this->render('search/index.html.twig', [
            'watches' => $watches,
            'query' => $query
        ]);
    }
} 