<?php

namespace App\Controller;

use App\Entity\Watch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\WatchType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WatchController extends AbstractController
{
    #[Route('/watch/edit/{id}', name: 'app_watch_edit')]
    public function edit(Watch $watch, Request $request, EntityManagerInterface $em): Response
    {
        // Vérifier si l'utilisateur est l'auteur de la montre
        if ($this->getUser() !== $watch->getAuthor()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cette montre.');
        }

        $form = $this->createForm(WatchType::class, $watch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer l'upload de la nouvelle image
            $pictureFile = $form->get('picture')->getData();
            
            if ($pictureFile) {
                $newFilename = uniqid().'.'.$pictureFile->guessExtension();
                
                // Supprimer l'ancienne image si elle existe
                if ($watch->getPicture()) {
                    $oldFilePath = $this->getParameter('watch_pictures_directory') . '/' . $watch->getPicture();
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                $pictureFile->move(
                    $this->getParameter('watch_pictures_directory'),
                    $newFilename
                );
                
                $watch->setPicture($newFilename);
            }

            $em->flush();
            $this->addFlash('success', 'Votre montre a été modifiée avec succès !');
            return $this->redirectToRoute('app_particuliers');
        }

        return $this->render('watch/index.html.twig', [
            'form' => $form->createView(),
            'watch' => $watch
        ]);
    }
}