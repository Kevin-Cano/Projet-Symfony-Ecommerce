<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Watch;
use App\Form\WatchType;

#[IsGranted('ROLE_ADMIN')]
final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupérer tous les utilisateurs
        $users = $entityManager->getRepository(User::class)->findAll();
        
        // Récupérer toutes les montres
        $watches = $entityManager->getRepository(Watch::class)->findAll();

        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'watches' => $watches,
        ]);
    }

    #[Route('/admin/toggle-role/{id}', name: 'admin_toggle_role')]
    public function toggleRole(User $user, EntityManagerInterface $entityManager): Response
    {
        if ($user === $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier vos propres droits.');
            return $this->redirectToRoute('app_admin');
        }

        if ($user->getEmail() === $_ENV['ADMIN_EMAIL']) {
            $this->addFlash('error', 'Impossible de modifier les droits de l\'administrateur principal.');
            return $this->redirectToRoute('app_admin');
        }

        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles)) {
            $adminCount = $entityManager->getRepository(User::class)->count(['roles' => '["ROLE_ADMIN"]']);
            if ($adminCount <= 1) {
                $this->addFlash('error', 'Impossible de retirer les droits admin : il doit rester au moins un administrateur.');
                return $this->redirectToRoute('app_admin');
            }
            $user->setRoles(['ROLE_USER']);
            $message = 'Droits administrateur retirés avec succès.';
        } else {
            $user->setRoles(['ROLE_ADMIN']);
            $message = 'Droits administrateur accordés avec succès.';
        }

        $entityManager->flush();
        $this->addFlash('success', $message);
        
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/delete/{id}', name: 'admin_delete_user')]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): Response
    {
        if ($user === $this->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            return $this->redirectToRoute('app_admin');
        }

        if ($user->getEmail() === $_ENV['ADMIN_EMAIL']) {
            $this->addFlash('error', 'Impossible de supprimer l\'administrateur principal.');
            return $this->redirectToRoute('app_admin');
        }

        try {
            // Récupérer toutes les montres de l'utilisateur
            $watches = $entityManager->getRepository(Watch::class)->findBy(['author' => $user]);
            
            // Supprimer chaque montre
            foreach ($watches as $watch) {
                // Supprimer l'image si elle existe
                if ($watch->getPicture()) {
                    $picturePath = $this->getParameter('watch_pictures_directory') . '/' . $watch->getPicture();
                    if (file_exists($picturePath)) {
                        unlink($picturePath);
                    }
                }
                $entityManager->remove($watch);
            }

            // Supprimer l'utilisateur
            $entityManager->remove($user);
            $entityManager->flush();
            
            $this->addFlash('success', 'L\'utilisateur et ses montres ont été supprimés avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression.');
        }
        
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/watches', name: 'admin_watches')]
    public function manageWatches(EntityManagerInterface $entityManager): Response
    {
        $watches = $entityManager->getRepository(Watch::class)->findAll();
        
        return $this->render('admin/watches.html.twig', [
            'watches' => $watches,
        ]);
    }

    #[Route('/admin/watch/edit/{id}', name: 'admin_watch_edit')]
    public function editWatch(Watch $watch, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WatchType::class, $watch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer l'upload d'image uniquement pour les montres de particuliers
            if ($watch->getAuthor()) {
                $pictureFile = $form->get('picture')->getData();
                if ($pictureFile) {
                    // Supprimer l'ancienne image si elle existe
                    if ($watch->getPicture()) {
                        $oldPicturePath = $this->getParameter('watch_pictures_directory') . '/' . $watch->getPicture();
                        if (file_exists($oldPicturePath)) {
                            unlink($oldPicturePath);
                        }
                    }

                    // Upload de la nouvelle image
                    $newFilename = uniqid().'.'.$pictureFile->guessExtension();
                    $pictureFile->move(
                        $this->getParameter('watch_pictures_directory'),
                        $newFilename
                    );
                    $watch->setPicture($newFilename);
                }
            } else {
                // Pour les montres de la boutique, l'image vient de l'API
                // et est déjà stockée dans l'URL
                $pictureUrl = $form->get('picture')->getData();
                if ($pictureUrl) {
                    $watch->setPicture($pictureUrl);
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'La montre a été modifiée avec succès.');
            return $this->redirectToRoute('admin_watches');
        }

        return $this->render('admin/watch_edit.html.twig', [
            'form' => $form->createView(),
            'watch' => $watch
        ]);
    }

    #[Route('/admin/watch/delete/{id}', name: 'admin_watch_delete')]
    public function deleteWatch(Watch $watch, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($watch);
        $entityManager->flush();
        
        $this->addFlash('success', 'La montre a été supprimée avec succès.');
        return $this->redirectToRoute('admin_watches');
    }

    #[Route('/admin/watch/stock/{id}', name: 'admin_watch_stock', methods: ['POST'])]
    public function updateStock(Watch $watch, Request $request, EntityManagerInterface $entityManager): Response
    {
        $stock = $request->request->get('stock');
        if ($stock !== null) {
            $watch->getStock()->setWatchStock((int) $stock);
            $entityManager->flush();
            return $this->json(['success' => true, 'newStock' => $stock]);
        }
        return $this->json(['success' => false], 400);
    }
}
