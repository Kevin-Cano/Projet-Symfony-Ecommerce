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
        return $this->render('home/index.html.twig', [
            'watches' => $watchRepository->findSixMostExpensive(),
            'otherWatches' => $watchRepository->findSixOtherWatches()
        ]);
    }
}
