<?php

namespace App\Controller;

use App\Entity\Watch;
use App\Repository\WatchRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use App\Form\WatchType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class WatchController extends AbstractController
{
    #[Route('/watch/edit/{id}', name: 'app_watch_edit')]
    public function edit(Watch $watch, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
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
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();

                try {
                    // Supprimer l'ancienne image si elle existe
                    if ($watch->getPicture()) {
                        $oldFilePath = $this->getParameter('kernel.project_dir') . '/public/uploads/watch_pictures/' . $watch->getPicture();
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }

                    // Upload la nouvelle image
                    $pictureFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/watch_pictures',
                        $newFilename
                    );
                    
                    // Mettre à jour le nom de l'image dans la base de données
                    $watch->setPicture($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }
            }

            $em->flush();
            $this->addFlash('success', 'Votre montre a été modifiée avec succès !');
            return $this->redirectToRoute('app_particuliers');
        }

        return $this->render('watch/edit.html.twig', [
            'form' => $form->createView(),
            'watch' => $watch
        ]);
    }

    #[Route('/api/watches', name: 'api_watch_list', methods: ['GET'])]
    public function list(WatchRepository $watchRepository): JsonResponse
    {
        $watches = $watchRepository->findAll();
        
        return $this->json($watches, 200, [], ['groups' => 'watch:read']);
    }

    #[Route('/api/watches/{id}', name: 'api_watch_show', methods: ['GET'])]
    public function show(Watch $watch): JsonResponse
    {
        return $this->json($watch, 200, [], ['groups' => 'watch:read']);
    }

    #[Route('/api/watches/new', name: 'api_watch_create', methods: ['POST'])]
    public function createWatch(Request $request, EntityManagerInterface $entityManager, Security $security): JsonResponse
    {
        $watch = new Watch();
        $form = $this->createForm(WatchType::class, $watch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $security->getUser();
            $watch->setAuthor($user);

            $entityManager->persist($watch);
            $entityManager->flush();

            return $this->json($watch, 201, [], ['groups' => 'watch:read']);
        }

        return $this->json(['error' => 'Invalid data'], 400);
    }
}