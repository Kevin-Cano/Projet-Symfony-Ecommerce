<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Entity\Watch;
use App\Entity\Invoice;
use App\Entity\InvoiceDetail;
use App\Entity\CartItem;
use App\Form\UserType;

final class AccountController extends AbstractController
{
    private Security $security;
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        Security $security, 
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/account', name: 'app_account')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            // Rediriger vers la page de connexion si non connecté
            return $this->redirectToRoute('app_login');
        }
        
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        
        // Récupérer les factures de l'utilisateur
        $invoices = $entityManager->getRepository(Invoice::class)->findBy(
            ['user' => $this->getUser()],
            ['createdAt' => 'DESC']
        );
        
        // Passer l'utilisateur connecté et les factures au template
        return $this->render('account/index.html.twig', [
            'user' => $user,
            'watches' => $user->getWatches()->toArray(),
            'invoices' => $invoices
        ]);
    }

    #[Route('/account/add-funds', name: 'app_account_add_funds', methods: ['POST'])]
    public function addFunds(Request $request): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour effectuer cette action.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Récupérer le montant
        $amount = $request->request->get('amount');
        
        // Validation simple du montant
        if (!$amount || !is_numeric($amount) || $amount <= 0) {
            return $this->json([
                'success' => false,
                'message' => 'Veuillez entrer un montant valide.'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = $this->getUser();
            $currentBalance = $user->getBalance() ?? 0;
            $newBalance = $currentBalance + floatval($amount);
            
            $user->setBalance($newBalance);
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Fonds ajoutés avec succès !',
                'newBalance' => $newBalance
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'ajout des fonds.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/account/update-phone', name: 'app_account_update_phone', methods: ['POST'])]
    public function updatePhone(Request $request): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            throw new AccessDeniedException('Vous devez être connecté pour effectuer cette action.');
        }

        $phoneNumber = $request->request->get('phoneNumber');

        if (!$phoneNumber) {
            return $this->json([
                'success' => false,
                'message' => 'Le numéro de téléphone est requis.'
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!preg_match('/^\+?[0-9]{10,15}$/', $phoneNumber)) {
            return $this->json([
                'success' => false,
                'message' => 'Format de numéro de téléphone invalide.'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user->setPhoneNumber($phoneNumber);
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Numéro de téléphone mis à jour avec succès !'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/account/update-password', name: 'app_account_update_password', methods: ['POST'])]
    public function updatePassword(Request $request): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            throw new AccessDeniedException('Vous devez être connecté pour effectuer cette action.');
        }

        $currentPassword = $request->request->get('currentPassword');
        $newPassword = $request->request->get('newPassword');
        $confirmPassword = $request->request->get('confirmPassword');

        // Vérification du mot de passe actuel
        if (!$this->passwordHasher->isPasswordValid($user, $currentPassword)) {
            return $this->json([
                'success' => false,
                'message' => 'Le mot de passe actuel est incorrect.'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Validation du nouveau mot de passe
        if (strlen($newPassword) < 8) {
            return $this->json([
                'success' => false,
                'message' => 'Le mot de passe doit contenir au moins 8 caractères.'
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($newPassword !== $confirmPassword) {
            return $this->json([
                'success' => false,
                'message' => 'Les mots de passe ne correspondent pas.'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $encodedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($encodedPassword);
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Mot de passe mis à jour avec succès !'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/account/delete-account', name: 'app_account_delete_account', methods: ['POST'])]
    public function deleteAccount(): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            throw new AccessDeniedException('Vous devez être connecté pour effectuer cette action.');
        }

        try {
            // Récupérer l'EntityManager
            $em = $this->entityManager;

            // Commencer une transaction
            $em->beginTransaction();

            try {
                // Supprimer l'utilisateur (et toutes ses relations en cascade)
                $em->remove($user);
                $em->flush();

                // Si tout s'est bien passé, valider la transaction
                $em->commit();

                // Déconnexion de l'utilisateur
                $this->security->logout(false);

                return $this->json([
                    'success' => true,
                    'message' => 'Compte et données associées supprimés avec succès.'
                ]);
            } catch (\Exception $e) {
                // En cas d'erreur, annuler la transaction
                $em->rollback();
                throw $e;
            }
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la suppression du compte.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/account/update-personal-info', name: 'app_account_update_personal_info', methods: ['POST'])]
    public function updatePersonalInfo(Request $request): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour effectuer cette action.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $field = $request->request->get('field');
        $value = $request->request->get('value');
        
        if (!$field || !$value) {
            return $this->json([
                'success' => false,
                'message' => 'Champ ou valeur manquant.'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = $this->getUser();
            
            // Mettre à jour le champ approprié
            switch ($field) {
                case 'firstName':
                    $user->setFirstName($value);
                    break;
                case 'lastName':
                    $user->setLastName($value);
                    break;
                case 'deliveryAddress':
                    $user->setDeliveryAddress($value);
                    break;
                case 'postalCode':
                    $user->setPostalCode($value);
                    break;
                default:
                    return $this->json([
                        'success' => false,
                        'message' => 'Champ non reconnu.'
                    ], Response::HTTP_BAD_REQUEST);
            }
            
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            
            return $this->json([
                'success' => true,
                'message' => 'Information mise à jour avec succès !'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/account/update-username', name: 'app_account_update_username', methods: ['POST'])]
    public function updateUsername(Request $request): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour effectuer cette action.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $newUsername = $request->request->get('userName');
        
        if (!$newUsername) {
            return $this->json([
                'success' => false,
                'message' => 'Le nom d\'utilisateur ne peut pas être vide.'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = $this->getUser();
            
            // Vérifier si le nom d'utilisateur existe déjà
            $existingUser = $this->entityManager->getRepository(User::class)
                ->findOneBy(['userName' => $newUsername]);
            
            if ($existingUser && $existingUser !== $user) {
                return $this->json([
                    'success' => false,
                    'message' => 'Ce nom d\'utilisateur est déjà pris.'
                ], Response::HTTP_BAD_REQUEST);
            }

            $user->setUserName($newUsername);
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Nom d\'utilisateur mis à jour avec succès !'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/account/update-profile-image', name: 'app_account_update_profile_image', methods: ['POST'])]
    public function updateProfileImage(Request $request): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour effectuer cette action.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $profileImage = $request->files->get('profileImage');
        
        if (!$profileImage) {
            return $this->json([
                'success' => false,
                'message' => 'Aucune image n\'a été envoyée.'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Valider le type de fichier
        $mimeType = $profileImage->getMimeType();
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if (!in_array($mimeType, $allowedTypes)) {
            return $this->json([
                'success' => false,
                'message' => 'Le format de l\'image n\'est pas supporté. Utilisez JPG, PNG, GIF ou WEBP.'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Valider la taille du fichier (max 2MB)
        if ($profileImage->getSize() > 2 * 1024 * 1024) {
            return $this->json([
                'success' => false,
                'message' => 'L\'image ne doit pas dépasser 2MB.'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = $this->getUser();
            
            // Supprimer l'ancienne image si elle existe
            $oldImage = $user->getProfilePicture();
            if ($oldImage) {
                $oldImagePath = $this->getParameter('kernel.project_dir') . '/public/uploads/profile/' . $oldImage;
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            
            // Générer un nom de fichier unique
            $newFilename = uniqid() . '.' . $profileImage->guessExtension();
            
            // Déplacer le fichier
            $profileImage->move(
                $this->getParameter('kernel.project_dir') . '/public/uploads/profile',
                $newFilename
            );
            
            // Mettre à jour l'utilisateur
            $user->setProfilePicture($newFilename);
            $this->entityManager->flush();
            
            return $this->json([
                'success' => true,
                'message' => 'Photo de profil mise à jour avec succès !',
                'imagePath' => $newFilename
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour de la photo de profil.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
    