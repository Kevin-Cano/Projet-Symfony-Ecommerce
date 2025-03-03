<?php

namespace App\Controller;

use App\Entity\Watch;
use App\Form\WatchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class EditController extends AbstractController{
    #[Route('/edit/{id}', name: 'app_watch_edit')]
    public function edit(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, int $id): Response
    {
        // Vérifier si l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            // Rediriger vers la page de connexion si non connecté
            return $this->redirectToRoute('app_login');
        }
        
        // Récupérer la montre à modifier
        $watch = $em->getRepository(Watch::class)->find($id);
        
        // Vérifier si la montre existe
        if (!$watch) {
            throw $this->createNotFoundException('La montre demandée n\'existe pas.');
        }
        
        // Vérifier si l'utilisateur est le propriétaire de la montre
        if ($watch->getAuthor() !== $user) {
            throw new AccessDeniedException('Vous n\'êtes pas autorisé à modifier cette montre.');
        }
        
        // Créer le formulaire
        $form = $this->createForm(WatchType::class, $watch);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form->get('picture')->getData();
            
            // Traiter la nouvelle image si elle est fournie
            if ($pictureFile) {
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();

                try {
                    $pictureFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/watch_pictures',
                        $newFilename
                    );
                    
                    // Supprimer l'ancienne image si elle existe
                    $oldPicture = $watch->getPicture();
                    if ($oldPicture) {
                        $oldPicturePath = $this->getParameter('kernel.project_dir') . '/public/uploads/watch_pictures/' . $oldPicture;
                        if (file_exists($oldPicturePath)) {
                            unlink($oldPicturePath);
                        }
                    }
                    
                    $watch->setPicture($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                }
            }
            
            // Mettre à jour la date de publication
            $watch->setPublicationDate(new \DateTime());
            
            $em->flush();
            
            $this->addFlash('success', 'Votre montre a été mise à jour avec succès !');
            return $this->redirectToRoute('app_account');
        }
        
        return $this->render('edit/index.html.twig', [
            'form' => $form->createView(),
            'watch' => $watch
        ]);
    }
}
