<?php

namespace App\Controller;

use App\Entity\Watch;
use App\Entity\Stock;
use App\Form\WatchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SellController extends AbstractController
{
    #[Route('/sell', name: 'app_sell')]
    public function sell(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        // Vérifier si l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            // Rediriger vers la page de connexion si non connecté
            return $this->redirectToRoute('app_login');
        }

        $watch = new Watch();
        // Associer l'utilisateur connecté à la montre
        $watch->setAuthor($user);
        
        // Créer et associer un nouveau Stock
        $stock = new Stock();
        $stock->setWatchStock(1); // Par défaut 1 montre en stock
        $stock->setWatch($watch);
        $watch->setStock($stock);
        
        $form = $this->createForm(WatchType::class, $watch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form->get('picture')->getData();
            
            if ($pictureFile) {
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();

                try {
                    $pictureFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/watch_pictures',
                        $newFilename
                    );
                    
                    $watch->setPicture($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            // Définir la date de publication
            $watch->setPublicationDate(new \DateTime());
            
            $em->persist($watch);
            $em->persist($stock);
            $em->flush();

            $this->addFlash('success', 'Votre montre a été mise en vente avec succès !');
            return $this->redirectToRoute('app_particuliers');
        }

        return $this->render('sell/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

