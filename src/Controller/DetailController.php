<?php

namespace App\Controller;

use App\Entity\Watch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DetailController extends AbstractController
{
    #[Route('/detail/{id}', name: 'app_watch_details')]
    public function index(Watch $watch): Response
    {
        return $this->render('collection/details.html.twig', [
            'watch' => $watch,
        ]);
    }
}
